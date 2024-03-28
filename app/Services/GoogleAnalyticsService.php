<?php

namespace App\Services;

use App\Models\UserApiKey;
use Google\Client;
use Google\Service\GoogleAnalyticsAdmin;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaDataStream;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaDataStreamWebStreamData;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaMeasurementProtocolSecret;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaProperty;
use Illuminate\Support\Facades\Auth;
use Google\Service\GoogleAnalyticsAdmin\GoogleAnalyticsAdminV1betaAcknowledgeUserDataCollectionRequest;

class GoogleAnalyticsService
{
    protected $client;

    public function __construct()
    {
        $client = new Client();
        $client->setApplicationName('Stripe-Ga4 web stream');
        $client->setScopes([
            GoogleAnalyticsAdmin::ANALYTICS_EDIT,
        ]);
        // $client->setAuthConfig(storage_path('app/google-analytics/credentials.json'));

        $this->client = $client;
    }

    public function createDataStream($propertyId, $accessToken)
    {
        $client = new Client();
        $client->setAccessToken($accessToken);
        $analyticsAdminService = new GoogleAnalyticsAdmin($client);

        $dataStream = new GoogleAnalyticsAdminV1betaDataStream();

        $dataStream->setType('WEB_DATA_STREAM');
        $dataStream->setDisplayName('Stripe-ga4 stream');
        $webStreamData = new GoogleAnalyticsAdminV1betaDataStreamWebStreamData();
        $webStreamData->setDefaultUri(url('/'));
        $dataStream->setWebStreamData($webStreamData);

        try {
            $createdStream = $analyticsAdminService->properties_dataStreams->create(
                $propertyId,
                $dataStream
            );

            $stream = $createdStream->getWebStreamData()->getMeasurementId();
            if ($stream) {
                $user =   UserApiKey::where('user_id', Auth::user()->id)->first();
                if ($user) {
                    $user->ga4_measurement_id = $stream;
                    $user->ga4_property_id = $propertyId;
                    $user->save();
                } else {
                    $user = new  UserApiKey();
                    $user->user_id = Auth::user()->id;
                    $user->ga4_measurement_id = $stream;
                    $user->ga4_property_id = $propertyId;
                    $user->save();
                }
            }
            $streamid = strrchr($createdStream->name, "/");
            $streamid = intval(str_replace("/", "", $streamid));
            return  $streamid;
        } catch (\Exception $e) {
            // Handle exception
            return 'An error occurred: ' . $e->getMessage();
        }
    }

    public function createProperty($accessToken, $propertyDisplayName, $account)
    {

        
        $account_id = str_replace("accounts/", "", $account['name']);
        $account_id = intval($account_id);
        $client = new \Google\Client();
        $client->setAccessToken($accessToken);

        $analyticsAdminService = new GoogleAnalyticsAdmin($client);

        $property = new GoogleAnalyticsAdminV1betaProperty();
        $property->setDisplayName($propertyDisplayName);
        $property->setTimeZone('America/Los_Angeles');
        $property->setCurrencyCode('USD');
        $property->setParent('accounts/' . $account_id);
        $existing_property =  $this->ensurePropertyExists($accessToken, $propertyDisplayName, $account);

        if ($existing_property == null) {
            try {
                $createdProperty = $analyticsAdminService->properties->create($property);
                return $createdProperty->name;
            } catch (\Exception $e) {
                return 'An error occurred: ' . $e->getMessage();
            }
        } else {
            return   $existing_property;
        }
    }

    public function getAccounts($accessToken)
    {
        $client = new Client();
        $client->setAccessToken($accessToken);

        $analyticsAdminService = new GoogleAnalyticsAdmin($client);
        $list = array();
        // List accounts to find the account ID
        $accounts = $analyticsAdminService->accounts->listAccounts();
        foreach ($accounts as $account) {
            array_push($list, [
                'displayName' => $account->getDisplayName(),
                'name' => $account->getName() // This includes the full resource name, e.g., "accounts/123456789"
            ]);
        }
        return $list;
    }


    public function getMeasurementProtocolSecret($accessToken, $propertyId, $dataStreamId)
    {

     
        $client = new \Google\Client();
        $client->setAccessToken($accessToken);

        $analyticsAdminService = new GoogleAnalyticsAdmin($client);

        // Correctly format the streamId
        $parent = "{$propertyId}/dataStreams/{$dataStreamId}";

        $measurementProtocolSecret = new GoogleAnalyticsAdminV1betaMeasurementProtocolSecret();
        $measurementProtocolSecret->setDisplayName('Stripe-ga4 api protocol secret'); // Optional: set a human-readable name for the secret

        $this->acknowledgeConsent($accessToken, $propertyId);
        // try {
        $createdSecret = $analyticsAdminService->properties_dataStreams_measurementProtocolSecrets->create(
            $parent,
            $measurementProtocolSecret
        );
        $user =   UserApiKey::where('user_id', Auth::user()->id)->first();
        $user->ga4_api_secret = $createdSecret->secretValue;
        $user->save();
        return 'The api secrect has been  created';
        // } catch (\Exception $e) {
        //     echo 'An error occurred: ' . $e->getMessage();
        // }
    }

    public function acknowledgeConsent($accessToken, $property)
    {
        $client = new Client();
        $client->setAccessToken($accessToken); // Ensure this is the access token obtained via OAuth.

        $analyticsAdminService = new GoogleAnalyticsAdmin($client);

        $requestBody = new GoogleAnalyticsAdminV1betaAcknowledgeUserDataCollectionRequest();
        $requestBody->setAcknowledgement("I acknowledge that I have the necessary privacy disclosures and rights from my end users for the collection and processing of their data, including the association of such data with the visitation information Google Analytics collects from my site and/or app property.");

        // try {
        $response = $analyticsAdminService->properties->acknowledgeUserDataCollection($property, $requestBody);
        return $response;
        // } catch (\Exception $e) {
        //     echo 'An error occurred: ' . $e->getMessage();
        // }
    }

    public function ensurePropertyExists($accessToken, $propertyDisplayName, $account)
    {

        $client = new \Google\Client();
        $client->setAccessToken($accessToken);
        $analyticsAdminService = new GoogleAnalyticsAdmin($client);

        // Step 1: List all properties for the account
        $properties = $analyticsAdminService->properties->listProperties(['filter' => 'parent:' . $account['name']])->getProperties();

        // Step 2: Search for a property with the name "Stripe-ga4"
        $existingPropertyId = null;
        foreach ($properties as $property) {
            if ($property->getDisplayName() === $propertyDisplayName) {
                $existingPropertyId = $property->getName();
                break;
            }
        }

        // If the property exists, return its ID
        if ($existingPropertyId) {
            return $existingPropertyId;
        }
        return null;
    }
}
