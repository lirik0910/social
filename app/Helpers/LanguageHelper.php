<?php

namespace App\Helpers;


use App\Traits\ReflectionTrait;
use Illuminate\Support\Str;

class LanguageHelper
{
    use ReflectionTrait;

    const LOCALE_EN = 'en';
    const LOCALE_RU = 'ru';

    /**
     * Return languages list
     *
     * @return array
     */
    public static function languageList()
    {
        return [
            1   => 'Аҧсуа',
            2   => 'Afaraf',
            3   => 'Afrikaans',
            4   => 'Akan',
            5   => 'Shqip',
            6   => 'አማርኛ',
            7   => 'العربية',
            8   => 'Aragonés',
            9   => 'Հայերեն',
            10  => 'অসমীয়া',
            11  => 'Авар мацӀ, магӀарул мацӀ',
            12  => 'Avesta',
            13  => 'Aymar aru',
            14  => 'Azərbaycan dili',
            15  => 'Bamanankan',
            16  => 'Башҡорт теле',
            17  => 'Euskara, euskera',
            18  => 'Беларуская',
            19  => 'বাংলা',
            20  => 'भोजपुरी',
            21  => 'Bislama',
            22  => 'Bosanski jezik',
            23  => 'Brezhoneg',
            24  => 'Български език',
            25  => 'ဗမာစာ',
            26  => 'Català',
            27  => 'Chamoru',
            28  => 'Нохчийн мотт',
            29  => 'ChiCheŵa, chinyanja',
            30  => '中文 (Zhōngwén), 汉语, 漢語',
            31  => 'Чӑваш чӗлхи',
            32  => 'Kernewek',
            33  => 'Corsu, lingua corsa',
            34  => 'ᓀᐦᐃᔭᐍᐏᐣ',
            35  => 'Hrvatski',
            36  => 'Česky, čeština',
            37  => 'Dansk',
            38  => 'ދިވެހި',
            39  => 'Nederlands, Vlaams',
            40  => 'English',
            41  => 'Esperanto',
            42  => 'Eesti, eesti keel',
            43  => 'Eʋegbe',
            44  => 'Føroyskt',
            45  => 'Vosa Vakaviti',
            46  => 'Suomi, suomen kieli',
            47  => 'Français, langue française',
            48  => 'Fulfulde, Pulaar, Pular',
            49  => 'Galego',
            50  => 'Ქართული',
            51  => 'Deutsch',
            52  => 'Ελληνικά',
            53  => 'Avañeẽ',
            54  => 'ગુજરાતી',
            55  => 'Kreyòl ayisyen',
            56  => 'Hausa, هَوُسَ',
            57  => 'עברית',
            58  => 'Otjiherero',
            59  => 'हिन्दी, हिंदी',
            60  => 'Hiri Motu',
            61  => 'Magyar',
            62  => 'Interlingua',
            63  => 'Bahasa Indonesia',
            64  => 'Interlingue',
            65  => 'Gaeilge',
            66  => 'Asụsụ Igbo',
            67  => 'Iñupiaq, Iñupiatun',
            68  => 'Ido',
            69  => 'Íslenska',
            70  => 'Italiano',
            71  => 'ᐃᓄᒃᑎᑐᑦ',
            72  => '日本語 (にほんご／にっぽんご)',
            73  => 'Basa Jawa',
            74  => 'Kalaallisut, kalaallit oqaasii',
            75  => 'ಕನ್ನಡ',
            76  => 'Kanuri',
            77  => 'कश्मीरी, كشميري‎',
            78  => 'Қазақ тілі',
            79  => 'ភាសាខ្មែរ',
            80  => 'Gĩkũyũ',
            81  => 'Ikinyarwanda',
            82  => 'Кыргыз тили',
            83  => 'Коми кыв',
            84  => 'KiKongo',
            85  => '한국어 (韓國語), 조선말 (朝鮮語)',
            86  => 'Kurdî, كوردی‎',
            87  => 'Kuanyama',
            88  => 'Latine, lingua latina',
            89  => 'Lëtzebuergesch',
            90  => 'Luganda',
            91  => 'Limburgs',
            92  => 'Lingála',
            93  => 'ພາສາລາວ',
            94  => 'Lietuvių kalba',
            95  => 'Luba-Katanga',
            96  => 'Latviešu valoda',
            97  => 'Gaelg, Gailck',
            98  => 'Македонски јазик',
            99  => 'Malagasy fiteny',
            100 => 'Bahasa Melayu, بهاس ملايو‎',
            101 => 'മലയാളം',
            102 => 'Malti',
            103 => 'Te reo Māori',
            104 => 'मराठी',
            105 => 'Kajin M̧ajeļ',
            106 => 'Монгол',
            107 => 'Ekakairũ Naoero',
            108 => 'Diné bizaad, Dinékʼehǰí',
            109 => 'Norsk bokmål',
            110 => 'IsiNdebele',
            111 => 'नेपाली',
            112 => 'Owambo',
            113 => 'Norsk nynorsk',
            114 => 'Norsk',
            115 => 'ꆈꌠ꒿ Nuosuhxop',
            116 => 'IsiNdebele',
            117 => 'Occitan',
            118 => 'ᐊᓂᔑᓈᐯᒧᐎᓐ',
            119 => 'Ѩзыкъ словѣньскъ',
            120 => 'Afaan Oromoo',
            121 => 'ଓଡ଼ିଆ',
            122 => 'Ирон æвзаг',
            123 => 'ਪੰਜਾਬੀ, پنجابی‎',
            124 => 'पाऴि',
            125 => 'فارسی',
            126 => 'Polski',
            127 => 'پښتو',
            128 => 'Português',
            129 => 'Runa Simi, Kichwa',
            130 => 'Rumantsch grischun',
            131 => 'KiRundi',
            132 => 'Română',
            133 => 'Русский язык',
            134 => 'संस्कृतम्',
            135 => 'Sardu',
            136 => 'सिन्धी, سنڌي، سندھی‎',
            137 => 'Davvisámegiella',
            138 => 'Gagana faa Samoa',
            139 => 'Yângâ tî sängö',
            140 => 'Српски језик',
            141 => 'Gàidhlig',
            142 => 'ChiShona',
            143 => 'සිංහල',
            144 => 'Slovenčina',
            145 => 'Slovenščina',
            146 => 'Soomaaliga, af Soomaali',
            147 => 'Sesotho',
            148 => 'Español, castellano',
            149 => 'Basa Sunda',
            150 => 'Kiswahili',
            151 => 'SiSwati',
            152 => 'Svenska',
            153 => 'தமிழ்',
            154 => 'తెలుగు',
            155 => 'Тоҷикӣ, toğikī, تاجیکی‎',
            156 => 'ไทย',
            157 => 'ትግርኛ',
            158 => 'བོད་ཡིག',
            159 => 'Türkmen, Түркмен',
            160 => 'Wikang Tagalog',
            161 => 'Setswana',
            162 => 'Faka Tonga',
            163 => 'Türkçe',
            164 => 'Xitsonga',
            165 => 'Татарча, tatarça, تاتارچا‎',
            166 => 'Twi',
            167 => 'Reo Tahiti',
            168 => 'Uyƣurqə, ئۇيغۇرچە‎',
            169 => 'Українська',
            170 => 'اردو',
            171 => 'Zbek, Ўзбек, أۇزبېك‎',
            172 => 'Tshivenḓa',
            173 => 'Tiếng Việt',
            174 => 'Volapük',
            175 => 'Walon',
            176 => 'Cymraeg',
            177 => 'Wollof',
            178 => 'Frysk',
            179 => 'IsiXhosa',
            180 => 'ייִדיש',
            181 => 'Yorùbá',
            182 => 'Saɯ cueŋƅ, Saw cuengh',
        ];
    }

    /**
     * Return ids of available languages
     *
     * @return array
     */
    public static function fetchLanguageIds()
    {
        return array_keys(self::languageList());
    }

    /**
     * Filter input ids and return only available ones
     *
     * @param array $ids
     *
     * @return array
     */
    public static function filterIds(array $ids){
        return array_intersect(self::fetchLanguageIds(), $ids);
    }

    /**
     * @param $current_uri
     * @return array
     * @throws \ReflectionException
     */
    public static function getLocaleChangeUrls($current_uri)
    {
        $current_locale = app()->getLocale();

        $available_locales = LanguageHelper::availableParams('locale');

        $locales = [];

        foreach ($available_locales as $available_locale) {
            $locales[$available_locale]['title'] = __('common.language_' . $available_locale);

            if ($current_locale === $available_locale) {
                $locales[$available_locale]['uri'] = $current_uri;
            } else {
                if ($current_locale === LanguageHelper::LOCALE_EN) {
                    $locales[$available_locale]['uri'] = $available_locale . '/' . $current_uri;
                } else {
                    if ($available_locale !== LanguageHelper::LOCALE_EN) {
                        $locales[$available_locale]['uri'] = $available_locale . '/' . Str::after($current_uri, '/');
                    } else {
                        $locales[$available_locale]['uri'] = Str::after($current_uri, '/');
                    }
                }
            }
        }

        return $locales;
    }

    public static function getLocaleUrlPrefix()
    {
        $current_locale = app()->getLocale();

        return $current_locale === 'en'
            ? ''
            : $current_locale . '/';
    }
}
