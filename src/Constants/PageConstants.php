<?php


namespace App\Constants;

use App\Entity\FAQSection;
use App\Entity\HTMLSection;
use App\Entity\SlideShowSection;
use App\Entity\TilesEventsSection;

class PageConstants
{
    const SECTION_TYPE_HTML = "html";
    const SECTION_TYPE_FAQ = "faq";
    const SECTION_TYPE_SLIDES = "slides";
    const SECTION_TYPE_TILES = "tiles";

    const SECTION_CLASS_MAP = [
        self::SECTION_TYPE_HTML => HTMLSection::class,
        self::SECTION_TYPE_FAQ => FAQSection::class,
        self::SECTION_TYPE_SLIDES => SlideShowSection::class,
        self::SECTION_TYPE_TILES => TilesEventsSection::class,
    ];
}