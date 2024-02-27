# Laravel FCM HTTP v1 Package Documentation

Introduction
Welcome to the documentation for the Laravel FCM HTTP v1 Package. This package allows Laravel users to send push notifications using Firebase Cloud Messaging (FCM) HTTP v1 API.

Installation
1. Composer Installation
Install the package via Composer:

        composer require kmsohelrana/fcmhttpv1

2. Configuration
After installation, publish the configuration file:

        php artisan vendor:publish --provider="Kmsohelrana\Fcmhttpv1\Providers\FcmServiceProvider" --tag=config

This will create a fcm_config.php file in your config directory.

Usage
Sending a Push Notification
To send a push notification, you can use the FcmHttpV1 facade:

use Kmsohelrana\Fcmhttpv1\FirebaseNotification;

        $notify = new FirebaseNotification();
         $response = $notify->setToken("eFmeOQMiSKSkk9Q4WJmh5Y:APA91bHIql-59sh83t68ncRGalpT1H................")
            ->setNotificationType("data") // both way you can send .it can be data or notification you can use
            ->setMessage([
                "title" => "Breaking Town hall Session will be start at the Evening",
                "body" => "Some enjoyable games have been arranged for you as part of our Town hall Program Today. There will be Presents for the winners as well!  You all are cordially invited.",
                .........................
                ..................
            ])->send();


Conclusion
Congratulations! You have successfully installed and used the Laravel FCM HTTP v1 Package for push notifications.

For more information, refer to the FCM HTTP v1 documentation.



