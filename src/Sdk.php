<?php /** @noinspection ALL */

namespace Visacheck\Visacheck;


use GuzzleHttp\Client;
use Visacheck\Visacheck\Exception\ResourceNotFoundException;
use Visacheck\Visacheck\Exception\VisacheckException;
use Visacheck\Visacheck\Resources\ResourceInterface;
use Visacheck\Visacheck\Services\ServiceInterface;

/**
 * The main SDK class for accessing the resources, and services on the Dorcas API.
 * It provides some methods that allow you to easily create, and use resources and services.
 *
 *
 * @method \Visacheck\Visacheck\Resources\Checks\Check                  createCheckResource(string $id = null)
 * @method \Visacheck\Visacheck\Resources\Checks\Checklist              createChecklistResource(string $id = null)
 * @method \Visacheck\Visacheck\Resources\Company                       createCompanyResource(string $id = null)
 * @method \Visacheck\Visacheck\Resources\Common\Country                createCountryResource(string $id = null)
 * @method \Visacheck\Visacheck\Resources\Common\Role                   createRoleResource(string $id = null)
 * @method \Visacheck\Visacheck\Resources\Services\Service              createServiceResource(string $id = null)
 * @method \Visacheck\Visacheck\Resources\Services\ServiceType          createServiceTypeResource(string $id = null)
 * @method \Visacheck\Visacheck\Resources\Common\State                  createStateResource(string $id = null)
 * @method \Visacheck\Visacheck\Resources\Users\User                    createUserResource(string $id = null)
 * @method \Visacheck\Visacheck\Resources\Vehicle\Make                  createVehicleMakeResource(string $id = null)
 * @method \Visacheck\Visacheck\Resources\Vehicle\Model                 createVehicleModelResource(string $id = null)
 * @method \Visacheck\Visacheck\Resources\Vehicle\Type                  createVehicleTypeResource(string $id = null)
 * @method \Visacheck\Visacheck\Resources\Vehicle\Usage                 createVehicleUsageResource(string $id = null)
 * @method \Visacheck\Visacheck\Resources\Vehicle\Vehicle               createVehicleResource(string $id = null)
 * @method \Visacheck\Visacheck\Services\Identity\Company               createCompanyService()
 * @method \Visacheck\Visacheck\Services\Identity\ForgotPassword        createForgotPasswordService()
 * @method \Visacheck\Visacheck\Services\Identity\PasswordLogin         createPasswordLoginService()
 * @method \Visacheck\Visacheck\Services\Identity\Profile               createProfileService()
 * @method \Visacheck\Visacheck\Services\Identity\Registration          createRegistrationService()
 * @method \Visacheck\Visacheck\Services\Identity\ResetPassword         createResetPasswordService()
 * @method \Visacheck\Visacheck\Services\Identity\VerifyAccount         createVerifyAccountService()
 * @method \Visacheck\Visacheck\Services\GeoCode                        createGeoCodeService()
 * @method \Visacheck\Visacheck\Services\ViewConfiguration              createViewConfigurationService()
 *
 */
class Sdk
{
    const VERSION = '0.0.1';

    /**
     * The configuration options to be used throughout the Sdk.
     *
     * @var array
     */
    private $args;

    /**
     * @var UrlRegistry
     */
    private $urlRegistry;

    /**
     * @var \GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * @var Manifest
     */
    private $manifest;

    /** @var string|null */
    private $token;

    /**
     * Sdk constructor.
     *
     * - environment: the usage environment, it can be either "staging", or "production".
     *   Any value that isn't "production" is assumed to be "staging".
     * - credentials: an associative array of that contains the "id", "secret", and "token" keys.
     *   These represent the application client id and client secret generated for the request application; while the
     *   "token" key holds the value for the returned Bearer token from a successful authorization request.
     *
     * @param array $args Requires certain keys to be set for a proper configuration.
     */
    public function __construct(array $args = [])
    {
        if (empty($args['environment'])) {
            $args['environment'] = 'staging';
        }
        $this->checkCredentials($args);
        $this->args = $args;
        $this->urlRegistry = new UrlRegistry($args['environment']);
        $this->httpClient = http_client();
        $this->manifest = new Manifest();
        $this->token = data_get($args, 'credentials.token', null);
    }

    /**
     * Returns the OAuth client id.
     *
     * @return string
     */
    public function getClientId(): string
    {
        return (string) data_get($this->args, 'credentials.id');
    }

    /**
     * Returns the OAuth client secret.
     *
     * @return string
     */
    public function getClientSecret(): string
    {
        return (string) data_get($this->args, 'credentials.secret');
    }

    /**
     * Returns the HTTP client in use by the Sdk.
     *
     * @return Client
     */
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    /**
     * Returns the loaded manifest.
     *
     * @return Manifest
     */
    public function getManifest(): Manifest
    {
        return $this->manifest;
    }

    /**
     * Returns the instance.
     *
     * @return UrlRegistry
     */
    public function getUrlRegistry(): UrlRegistry
    {
        return $this->urlRegistry;
    }

    /**
     * Returns the authorization token value.
     *
     * @return string
     */
    public function getAuthorizationToken(): string
    {
        return (string) $this->token;
    }

    /**
     * Sets the authorization token.
     *
     * @param string $token
     *
     * @return Sdk
     */
    public function setAuthorizationToken(string $token): Sdk
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Checks the credentials configuration to make sure it is valid.
     *
     * @param array $args
     *
     * @return bool
     * @throws DorcasException
     */
    private function checkCredentials(array $args = []): bool
    {
        if (empty($args['credentials'])) {
            throw new VisacheckException('You did not provide the Visacheck client credentials in the configuration.', $args);
        }
        $id = data_get($args, 'credentials.id', null);
        $secret = data_get($args, 'credentials.secret', null);
        if (empty($id)) {
            throw new VisacheckException('The client "id" key is absent in the credentials configuration.', $args);
        }
        if (empty($secret)) {
            throw new VisacheckException('The client "secret" key is absent in the credentials configuration.', $args);
        }
        return true;
    }

    /**
     * Creates a new resource client with the provided options.
     *
     * @param string $name
     * @param array  $options
     *
     * @return ResourceInterface
     */
    protected function createResourceClient(string $name, array $options = []): ResourceInterface
    {
        $entry = $this->manifest->getResource($name);
        # we check for the manifest entry
        if (empty($entry)) {
            throw new ResourceNotFoundException('Could not find the client for the requested resource '.$name);
        }
        $resource = $entry['namespace'] . '\\' . $entry['client'];
        return new $resource($this, ...$options);
    }

    /**
     * Creates a new service client with the provided options.
     *
     * @param string $name
     * @param array  $options
     *
     * @return ServiceInterface
     */
    protected function createServiceClient(string $name, array $options = []): ServiceInterface
    {
        $entry = $this->manifest->getService($name);
        # we check for the manifest entry
        if (empty($entry)) {
            throw new ResourceNotFoundException('Could not find the client for the requested service '.$name);
        }
        $service = $entry['namespace'] . '\\' . $entry['client'];
        return new $service($this, $options);
    }

    /**
     * Magic method.
     *
     * @param $name
     * @param $arguments
     *
     * @return ResourceInterface|ServiceInterface
     */
    public function __call($name, $arguments = null)
    {
        $isCreate = strpos($name, 'create') === 0;
        # check the action type
        if ($isCreate && strtolower(substr($name, -8)) === 'resource') {
            # we're attempting to create a resource client
            $name = substr($name, 6, -8);
            return $this->createResourceClient($name, $arguments);
        } elseif ($isCreate && strtolower(substr($name, -7)) === 'service') {
            # we're attempting to create a service client
            $name = substr($name, 6, -7);
            return $this->createServiceClient($name, $arguments);
        }
        throw new \BadMethodCallException('The method '.$name.' does not exist.');
    }
}