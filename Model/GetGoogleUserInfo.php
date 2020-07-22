<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\BackendGoogleSignOn\Model;

use Google_Service_Oauth2_Userinfo;
use Magento\Framework\Exception\LocalizedException;

/**
 * Verify Google authentication code and return user information
 */
class GetGoogleUserInfo
{
    /**
     * @var GetGoogleClient
     */
    private $getGoogleClient;

    /**
     * @var GoogleServiceOauthFactory
     */
    private $googleServiceOauthFactory;

    /**
     * @param GetGoogleClient $getGoogleClient
     * @param GoogleServiceOauthFactory $googleServiceOauthFactory
     */
    public function __construct(
        GetGoogleClient $getGoogleClient,
        GoogleServiceOauthFactory $googleServiceOauthFactory
    ) {
        $this->getGoogleClient = $getGoogleClient;
        $this->googleServiceOauthFactory = $googleServiceOauthFactory;
    }

    /**
     * Authenticate through the Google Auth code response
     *
     * @param string $authCode
     * @return Google_Service_Oauth2_Userinfo
     * @throws LocalizedException
     */
    public function execute(string $authCode): Google_Service_Oauth2_Userinfo
    {
        $googleClient = $this->getGoogleClient->execute();


        $token = $googleClient->fetchAccessTokenWithAuthCode($authCode);
        if (isset($token['error'])) {
            throw new LocalizedException(__($token['error_description']));
        }

        if (empty($token['access_token'])) {
            throw new LocalizedException(__('Empty token response'));
        }

        $oauthResponse = $this->googleServiceOauthFactory->create($googleClient);
        return $oauthResponse->userinfo->get();
    }
}
