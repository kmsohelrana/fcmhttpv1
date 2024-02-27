<?php

namespace Kmsohelrana\Fcmhttpv1;

use GuzzleHttp\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use Kmsohelrana\Fcmhttpv1\FcmTokenGenerate;
class FirebaseNotification
{


    protected $token;
    protected $topic;
    protected $notification_type;
    protected $customMessage;

    /**
     *Title of the notification.
     *@param string $title
     */
    public function setNotificationType($notification_type)
    {
        $this->notification_type = $notification_type;
        return $this;
    }
    /**
     *Token used to send notification to specific device. Unusable with setTopic() at same time.
     *@param string $string
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     *Topic of the notification. Unusable with setToken() at same time.
     *@param string $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
        return $this;
    }
    public function setMessage($customMessage)
    {
        $this->customMessage = $customMessage;
        return $this;
    }

    /**
     * Verify the conformity of the notification. If everything is ok, send the notification.
     */
    public function send()
    {
        // Token and topic combinaison verification
        if ($this->token != null && $this->topic != null) {
            throw new Exception("A notification need to have at least one target: token or topic. Please select only one type of target.");
        }

        // Empty token or topic verification
        if ($this->token == null && $this->topic == null) {
            throw new Exception("A notification need to have at least one target: token or topic. Please add a target using setToken() or setTopic().");
        }

        if ($this->token != null && !is_string($this->token)) {
            throw new Exception('Token format error. Received: ' . gettype($this->token) . ". Expected type: string");
        }

        return $this->prepareSend();
    }

    private function prepareSend()
    {
        if (isset($this->token)) {
            $data["token"] = $this->token;
        } elseif (isset($this->topic)) {
            $data["topic"] = $this->topic;
        }

        if (isset($this->customMessage)) {
            $data[$this->notification_type] = $this->customMessage;
        }

        $encodedData = json_encode([
            "message"=>$data
        ]);

        Log::info("Message Body :", [$encodedData]);

        return $this->handleSend($encodedData);
    }

    private function handleSend($encodedData)
    {
        $url = config('fcm_config.fcm_api_url');

        $oauthToken = FcmTokenGenerate::generateAccessToken();

        Log::info("Auth token :", [$oauthToken]);

        $headers = [
            'Authorization' => 'Bearer ' . $oauthToken,
            'Content-Type' =>  'application/json',
        ];

        $client = new Client();

        try {

            $request = $client->post($url, [
                'headers' => $headers,
                "body" => $encodedData,
            ]);

            if ( $request->getStatusCode() == 200 ) {

                $body = $request->getBody();

                Log::info('push notification send success');

                if ( $body ) {
                    return json_decode($body);
                }
            }

            Log::info("[Notification] SENT", [$encodedData]);

            $response = $request->getBody();

            return $response;
        } catch (Exception $e) {
            Log::error("[Notification] ERROR", [$e->getMessage()]);

            return $e;
        }
    }
}
