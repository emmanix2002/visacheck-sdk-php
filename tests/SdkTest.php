<?php

namespace Visacheck\Visacheck\Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Visacheck\Visacheck\Exception\VisacheckException;
use Visacheck\Visacheck\Manifest;
use Visacheck\Visacheck\Sdk;
use Visacheck\Visacheck\UrlRegistry;

class SdkTest extends TestCase
{
    /** @var Sdk */
    protected $sdk;

    public function setUp()
    {
        $this->sdk = new Sdk(['credentials' => ['id' => 1, 'secret' => 'fake-secret-code']]);
    }

    public function testCheckCredentialsFails()
    {
        $this->expectException(VisacheckException::class);
        $sdk = new Sdk();
    }

    public function testCheckCredentialsPasses()
    {
        $this->assertInstanceOf(Sdk::class, $this->sdk);
    }

    public function testGetClientId()
    {
        $this->assertEquals(1, $this->sdk->getClientId());
    }

    public function testGetClientSecret()
    {
        $this->assertEquals('fake-secret-code', $this->sdk->getClientSecret());
    }

    public function testGetHttpClient()
    {
        $this->assertInstanceOf(Client::class, $this->sdk->getHttpClient());
    }

    public function testGetManifest()
    {
        $this->assertInstanceOf(Manifest::class, $this->sdk->getManifest());
        $this->assertNotEmpty($this->sdk->getManifest()->data());
    }

    public function testGetUrlRegistry()
    {
        $this->assertInstanceOf(UrlRegistry::class, $this->sdk->getUrlRegistry());
        $this->assertEquals(UrlRegistry::ENVIRONMENTS['staging'], (string) $this->sdk->getUrlRegistry()->getUrl());
    }

    public function testGetAuthorizationToken()
    {
        $this->assertEmpty($this->sdk->getAuthorizationToken());
    }

    public function testSetAuthorizationToken()
    {
        $this->sdk->setAuthorizationToken('fake-token-value');
        $this->assertEquals('fake-token-value', $this->sdk->getAuthorizationToken());
    }

    /**
     * @dataProvider resourceProvider
     */
    public function testCreateResource($name, $expected)
    {
        $method = 'create' . $name . 'Resource';
        $resource = $this->sdk->{$method}();
        $this->assertInstanceOf($expected, $resource);
    }

    public function resourceProvider()
    {
        return [
            ['Check', \Visacheck\Visacheck\Resources\Checks\Check::class],
            ['Checklist', \Visacheck\Visacheck\Resources\Checks\Checklist::class],
            ['CheckSection', \Visacheck\Visacheck\Resources\Checks\CheckSection::class],
            ['Company', \Visacheck\Visacheck\Resources\Company::class],
            ['Country', \Visacheck\Visacheck\Resources\Common\Country::class],
            ['InsurancePolicy', \Visacheck\Visacheck\Resources\Insurance\Policy::class],
            ['Invite', \Visacheck\Visacheck\Resources\Common\Invite::class],
            ['Role', \Visacheck\Visacheck\Resources\Common\Role::class],
            ['Service', \Visacheck\Visacheck\Resources\Services\Service::class],
            ['ServiceType', \Visacheck\Visacheck\Resources\Services\ServiceType::class],
            ['State', \Visacheck\Visacheck\Resources\Common\State::class],
            ['User', \Visacheck\Visacheck\Resources\Users\User::class],
            ['Vehicle', \Visacheck\Visacheck\Resources\Vehicle\Vehicle::class],
            ['VehicleMake', \Visacheck\Visacheck\Resources\Vehicle\Make::class],
            ['VehicleModel', \Visacheck\Visacheck\Resources\Vehicle\Model::class],
            ['VehicleType', \Visacheck\Visacheck\Resources\Vehicle\Type::class],
            ['VehicleUsage', \Visacheck\Visacheck\Resources\Vehicle\Usage::class],
        ];
    }

    /**
     * @dataProvider serviceProvider
     */
    public function testCreateService($name, $expected)
    {
        $method = 'create' . $name . 'Service';
        $resource = $this->sdk->{$method}();
        $this->assertInstanceOf($expected, $resource);
    }

    public function serviceProvider()
    {
        return [
            ['Authorization', \Visacheck\Visacheck\Services\Identity\Authorization::class],
            ['Company', \Visacheck\Visacheck\Services\Identity\Company::class],
            ['ForgotPassword', \Visacheck\Visacheck\Services\Identity\ForgotPassword::class],
            ['GeoCode', \Visacheck\Visacheck\Services\GeoCode::class],
            ['PasswordLogin', \Visacheck\Visacheck\Services\Identity\PasswordLogin::class],
            ['Profile', \Visacheck\Visacheck\Services\Identity\Profile::class],
            ['Registration', \Visacheck\Visacheck\Services\Identity\Registration::class],
            ['ResetPassword', \Visacheck\Visacheck\Services\Identity\ResetPassword::class],
            ['VerifyAccount', \Visacheck\Visacheck\Services\Identity\VerifyAccount::class],
            ['ViewConfiguration', \Visacheck\Visacheck\Services\ViewConfiguration::class],
        ];
    }
}