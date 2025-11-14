<?php

namespace App\Enum;

enum Images: string
{
    case bgSetecem = 'bg-setecem.jpg';
    case bgSlots = 'bg-slots.jpg';
    case logoBlueSetecem = 'logo-blue-textless.svg';
    case logoBlueTextSetecem = 'logo-blue.png';
    case logoBlueEspSetecem = 'logo-blue-text-esp.svg';
    case logoWhiteSetecem = 'logo-white-textless.svg';
    case logoWhiteTextSetecem = 'logo-white.png';
    case logoWhiteEspSetecem = 'logo-white-text-esp.svg';
    case logoSlotsEyes = 'logo-slots-eyes.svg';
    case logoSlotsEyesWhite = 'logo-slots-eyes-white.svg';
    case profileSetecem = 'profile.png';
    case profileLightSetecem = 'profile-light.png';
    case logoSecuritasWhite = 'logo-securitas-white.svg';

    public static function list(): array
    {
        return [
            self::bgSetecem,
            self::bgSlots,
            self::logoBlueSetecem,
            self::logoBlueTextSetecem,
            self::logoBlueEspSetecem,
            self::logoWhiteSetecem,
            self::logoWhiteTextSetecem,
            self::logoWhiteEspSetecem,
            self::logoSlotsEyes,
            self::logoSlotsEyesWhite,
            self::profileSetecem,
            self::profileLightSetecem,
            self::logoSecuritasWhite
        ];
    }

    public static function listBg(): array
    {
        return [
            self::bgSetecem,
            self::bgSlots
        ];
    }
    public static function listLogo(): array {
        return [
            self::logoBlueSetecem,
            self::logoBlueTextSetecem,
            self::logoBlueEspSetecem,
            self::logoWhiteSetecem,
            self::logoWhiteTextSetecem,
            self::logoWhiteEspSetecem,
            self::logoSlotsEyes,
            self::logoSlotsEyesWhite,
            self::logoSecuritasWhite
        ];
    }

    public static function listIcon(): array {
        return [
            self::logoBlueSetecem,
            self::logoBlueTextSetecem,
            self::logoBlueEspSetecem,
            self::logoWhiteSetecem,
            self::logoWhiteTextSetecem,
            self::logoWhiteEspSetecem,
            self::logoSlotsEyes,
            self::logoSlotsEyesWhite,
            self::profileSetecem,
            self::profileLightSetecem,
            self::logoSecuritasWhite
        ];
    }

}