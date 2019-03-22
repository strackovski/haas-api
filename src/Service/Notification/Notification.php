<?php

namespace App\Service\Notification;

/**
 * Class Notification
 *
 * @package App\Service\Notification
 * @author
 * @copyright
 */
class Notification
{
    const SUPPORTED_DEVICES = [
        'IOS', 'ANDROID'
    ];

    /**
     * @var integer
     */
    private $badge;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $deviceToken;

    /**
     * @var string
     */
    private $deviceType;

    /**
     * Notification constructor.
     *
     * @param int    $badge
     * @param string $text
     * @param string $deviceToken
     * @param string $deviceType
     *
     * @throws \Exception
     */
    public function __construct($badge, $text, $deviceToken, $deviceType)
    {
        $this->badge = $badge;
        $this->text = $text;
        $this->deviceToken = $deviceToken;
        if (!in_array($deviceType, self::SUPPORTED_DEVICES)) {
            throw new \Exception(sprintf("Unsupported device type: %s", $deviceType));
        }
        $this->deviceType = $deviceType;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return [
            'badge' => $this->badge,
            'text' => $this->text,
            'deviceToken' => $this->deviceToken,
            'deviceType' => $this->deviceType
        ];
    }
}
