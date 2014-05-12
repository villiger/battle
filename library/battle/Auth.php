<?php

namespace Battle
{
    class Auth
    {
        private $provider;
        private $config;
        private $adapter;

        /**
         * @param $provider
         * @throws \Exception
         */
        public function __construct($provider)
        {
            $supportedProviders = array('google');
            if (! in_array($provider, $supportedProviders)) {
                throw new \Exception("Authentication provider '$provider' not supported.");
            }

            $this->provider = $provider;
            $this->config = array(
                "base_url" =>
                    array_key_exists('HTTPS', $_SERVER) ? 'https://' : 'http://' .
                    $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] .
                    "/login/callback",
                "providers" => array (
                    "Google" => array (
                        "enabled" => true,
                        "keys"    => array(
                            "id" => AUTH_GOOGLE_ID,
                            "secret" => AUTH_GOOGLE_SECRET
                        ),
                        "scope" =>
                            "https://www.googleapis.com/auth/plus.login " .
                            "https://www.googleapis.com/auth/userinfo.email",
                        "access_type"     => "offline",
                        "approval_prompt" => "force"
                    )
                )
            );
        }

        /**
         * @return \RedBean_OODBBean
         */
        public function authenticate()
        {
            $hybridauth = new \Hybrid_Auth($this->config);
            $this->adapter = $hybridauth->authenticate(ucfirst($this->provider));

            $profile = $this->adapter->getUserProfile();

            $user = $this->createUser(
                $profile->email,
                $profile->displayName,
                $profile->photoURL,
                $profile->identifier,
                true
            );

            $contacts = $this->adapter->getUserContacts();

            foreach ($contacts as $contact) {
                $friend = $this->createUser(
                    null,
                    $contact->displayName,
                    $contact->photoURL,
                    $contact->identifier,
                    false
                );

                // TODO: Connect $friend with $user
            }

            return $user;
        }

        /**
         * @param $email
         * @param $name
         * @param $image
         * @param $identifier
         * @param bool $active
         * @return \RedBean_OODBBean
         */
        private function createUser($email, $name, $image, $identifier, $active = true)
        {
            $providerField = $this->provider . "_id";

            if ($email !== null) {
                $user = \R::findOne('user', 'email LIKE ?', [ $email ]);
            } else {
                $user = \R::findOne('user', "$providerField = ?", [ $identifier ]);
            }

            if ($user === null) {
                $user = \R::dispense('user');
                $user->$providerField = $identifier;
                $user->email = $email;
                $user->name = $name;
                $user->image = $image;
                $user->is_active = $active;
                $user->updated = \R::isoDateTime();
                $user->created = \R::isoDateTime();
            } else {
                if (! $user->email) {
                    $user->email = $email;
                }

                if (! $user->is_active) {
                    $user->is_active = $active;
                }

                $user->name = $name;
                $user->image = $image;
                $user->updated = \R::isoDateTime();
            }

            \R::store($user);

            return $user;
        }
    }
}
