<?php

namespace App\Libraries\Sms;

use Illuminate\Support\Facades\Log;

abstract class AbstractSMS
{
    /**
     * cUrl resource
     *
     * @var false|resource|null
     */
    protected $ch = NULL;

    /**
     * Self instance
     *
     * @var null|self
     */
    protected static $self = NULL;

    /**
     * API url for sending sms
     *
     * @var string
     */
    protected $apiUrl = '';

    /**
     * Phone number that going to receive sms
     *
     * @var string
     */
    protected $phone = 'undefined';

    /**
     * AbstractSMS constructor. Init cURL.
     */
    protected function __construct()
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 30);
    }

    /**
     * Get instance of current class
     *
     * @param mixed ...$args
     *
     * @return self|null
     */
    public static function init(...$args)
    {
        if (!self::$self)
            self::$self = new static(...$args);

        return self::$self;
    }

    /**
     * Send sms messages
     *
     * @param mixed ...$args
     *
     * @return bool
     */
    public function send(...$args)
    {
        $this->configureRequest(...$args);

        return $this->sendSMS();
    }

    /**
     * Validate phone code and number.
     * If necessary function should be rewritten in child classes
     *
     * @param string $code
     * @param string $number
     *
     * @return bool
     */
    public function validate(string $code, string $number)
    {
        return true;
    }

    /**
     * List of phone codes by country
     *
     * @return array
     */
    public function phoneAssoc()
    {
        return [
            'af' => ['name' => 'Afghanistan', 'dial_code' => '+93', 'code' => 'af'],
            'ax' => ['name' => 'Åland Islands', 'dial_code' => '+358', 'code' => 'ax'],
            'al' => ['name' => 'Albania', 'dial_code' => '+355', 'code' => 'al'],
            'dz' => ['name' => 'Algeria', 'dial_code' => '+213', 'code' => 'dz'],
            'as' => ['name' => 'American Samoa', 'dial_code' => '+1684', 'code' => 'as'],
            'ad' => ['name' => 'Andorra', 'dial_code' => '+376', 'code' => 'ad'],
            'ao' => ['name' => 'Angola', 'dial_code' => '+244', 'code' => 'ao'],
            'ai' => ['name' => 'Anguilla', 'dial_code' => '+1264', 'code' => 'ai'],
            'aq' => ['name' => 'Antarctica', 'dial_code' => '+672', 'code' => 'aq'],
            'ag' => ['name' => 'Antigua and Barbuda', 'dial_code' => '+1268', 'code' => 'ag'],
            'ar' => ['name' => 'Argentina', 'dial_code' => '+54', 'code' => 'ar'],
            'am' => ['name' => 'Armenia', 'dial_code' => '+374', 'code' => 'am'],
            'aw' => ['name' => 'Aruba', 'dial_code' => '+297', 'code' => 'aw'],
            'au' => ['name' => 'Australia', 'dial_code' => '+61', 'code' => 'au'],
            'at' => ['name' => 'Austria', 'dial_code' => '+43', 'code' => 'at'],
            'az' => ['name' => 'Azerbaijan', 'dial_code' => '+994', 'code' => 'az'],
            'bs' => ['name' => 'Bahamas', 'dial_code' => '+1242', 'code' => 'bs'],
            'bh' => ['name' => 'Bahrain', 'dial_code' => '+973', 'code' => 'bh'],
            'bd' => ['name' => 'Bangladesh', 'dial_code' => '+880', 'code' => 'bd'],
            'bb' => ['name' => 'Barbados', 'dial_code' => '+1246', 'code' => 'bb'],
            'by' => ['name' => 'Belarus', 'dial_code' => '+375', 'code' => 'by'],
            'be' => ['name' => 'Belgium', 'dial_code' => '+32', 'code' => 'be'],
            'bz' => ['name' => 'Belize', 'dial_code' => '+501', 'code' => 'bz'],
            'bj' => ['name' => 'Benin', 'dial_code' => '+229', 'code' => 'bj'],
            'bm' => ['name' => 'Bermuda', 'dial_code' => '+1441', 'code' => 'bm'],
            'bt' => ['name' => 'Bhutan', 'dial_code' => '+975', 'code' => 'bt'],
            'bo' => ['name' => 'Bolivia, Plurinational State of bolivia', 'dial_code' => '+591', 'code' => 'bo'],
            'ba' => ['name' => 'Bosnia and Herzegovina', 'dial_code' => '+387', 'code' => 'ba'],
            'bw' => ['name' => 'Botswana', 'dial_code' => '+267', 'code' => 'bw'],
            'bv' => ['name' => 'Bouvet Island', 'dial_code' => '+47', 'code' => 'bv'],
            'br' => ['name' => 'Brazil', 'dial_code' => '+55', 'code' => 'br'],
            'io' => ['name' => 'British Indian Ocean Territory', 'dial_code' => '+246', 'code' => 'io'],
            'bn' => ['name' => 'Brunei Darussalam', 'dial_code' => '+673', 'code' => 'bn'],
            'bg' => ['name' => 'Bulgaria', 'dial_code' => '+359', 'code' => 'bg'],
            'bf' => ['name' => 'Burkina Faso', 'dial_code' => '+226', 'code' => 'bf'],
            'bi' => ['name' => 'Burundi', 'dial_code' => '+257', 'code' => 'bi'],
            'kh' => ['name' => 'Cambodia', 'dial_code' => '+855', 'code' => 'kh'],
            'cm' => ['name' => 'Cameroon', 'dial_code' => '+237', 'code' => 'cm'],
            'ca' => ['name' => 'Canada', 'dial_code' => '+1', 'code' => 'ca'],
            'cv' => ['name' => 'Cape Verde', 'dial_code' => '+238', 'code' => 'cv'],
            'ky' => ['name' => 'Cayman Islands', 'dial_code' => '+ 345', 'code' => 'ky'],
            'cf' => ['name' => 'Central African Republic', 'dial_code' => '+236', 'code' => 'cf'],
            'td' => ['name' => 'Chad', 'dial_code' => '+235', 'code' => 'td'],
            'cl' => ['name' => 'Chile', 'dial_code' => '+56', 'code' => 'cl'],
            'cn' => ['name' => 'China', 'dial_code' => '+86', 'code' => 'cn'],
            'cx' => ['name' => 'Christmas Island', 'dial_code' => '+61', 'code' => 'cx'],
            'cc' => ['name' => 'Cocos (Keeling) Islands', 'dial_code' => '+61', 'code' => 'cc'],
            'co' => ['name' => 'Colombia', 'dial_code' => '+57', 'code' => 'co'],
            'km' => ['name' => 'Comoros', 'dial_code' => '+269', 'code' => 'km'],
            'cg' => ['name' => 'Congo', 'dial_code' => '+242', 'code' => 'cg'],
            'cd' => ['name' => 'Congo, The Democratic Republic of the Congo', 'dial_code' => '+243', 'code' => 'cd'],
            'ck' => ['name' => 'Cook Islands', 'dial_code' => '+682', 'code' => 'ck'],
            'cr' => ['name' => 'Costa Rica', 'dial_code' => '+506', 'code' => 'cr'],
            'ci' => ['name' => 'Cote d\'Ivoire', 'dial_code' => '+225', 'code' => 'ci'],
            'hr' => ['name' => 'Croatia', 'dial_code' => '+385', 'code' => 'hr'],
            'cu' => ['name' => 'Cuba', 'dial_code' => '+53', 'code' => 'cu'],
            'cy' => ['name' => 'Cyprus', 'dial_code' => '+357', 'code' => 'cy'],
            'cz' => ['name' => 'Czech Republic', 'dial_code' => '+420', 'code' => 'cz'],
            'dk' => ['name' => 'Denmark', 'dial_code' => '+45', 'code' => 'dk'],
            'dj' => ['name' => 'Djibouti', 'dial_code' => '+253', 'code' => 'dj'],
            'dm' => ['name' => 'Dominica', 'dial_code' => '+1767', 'code' => 'dm'],
            'do' => ['name' => 'Dominican Republic', 'dial_code' => '+1849', 'code' => 'do'],
            'ec' => ['name' => 'Ecuador', 'dial_code' => '+593', 'code' => 'ec'],
            'eg' => ['name' => 'Egypt', 'dial_code' => '+20', 'code' => 'eg'],
            'sv' => ['name' => 'El Salvador', 'dial_code' => '+503', 'code' => 'sv'],
            'gq' => ['name' => 'Equatorial Guinea', 'dial_code' => '+240', 'code' => 'gq'],
            'er' => ['name' => 'Eritrea', 'dial_code' => '+291', 'code' => 'er'],
            'ee' => ['name' => 'Estonia', 'dial_code' => '+372', 'code' => 'ee'],
            'et' => ['name' => 'Ethiopia', 'dial_code' => '+251', 'code' => 'et'],
            'fk' => ['name' => 'Falkland Islands (Malvinas)', 'dial_code' => '+500', 'code' => 'fk'],
            'fo' => ['name' => 'Faroe Islands', 'dial_code' => '+298', 'code' => 'fo'],
            'fj' => ['name' => 'Fiji', 'dial_code' => '+679', 'code' => 'fj'],
            'fi' => ['name' => 'Finland', 'dial_code' => '+358', 'code' => 'fi'],
            'fr' => ['name' => 'France', 'dial_code' => '+33', 'code' => 'fr'],
            'gf' => ['name' => 'French Guiana', 'dial_code' => '+594', 'code' => 'gf'],
            'pf' => ['name' => 'French Polynesia', 'dial_code' => '+689', 'code' => 'pf'],
            'tf' => ['name' => 'French Southern Territories', 'dial_code' => '+262', 'code' => 'tf'],
            'ga' => ['name' => 'Gabon', 'dial_code' => '+241', 'code' => 'ga'],
            'gm' => ['name' => 'Gambia', 'dial_code' => '+220', 'code' => 'gm'],
            'ge' => ['name' => 'Georgia', 'dial_code' => '+995', 'code' => 'ge'],
            'de' => ['name' => 'Germany', 'dial_code' => '+49', 'code' => 'de'],
            'gh' => ['name' => 'Ghana', 'dial_code' => '+233', 'code' => 'gh'],
            'gi' => ['name' => 'Gibraltar', 'dial_code' => '+350', 'code' => 'gi'],
            'gr' => ['name' => 'Greece', 'dial_code' => '+30', 'code' => 'gr'],
            'gl' => ['name' => 'Greenland', 'dial_code' => '+299', 'code' => 'gl'],
            'gd' => ['name' => 'Grenada', 'dial_code' => '+1473', 'code' => 'gd'],
            'gp' => ['name' => 'Guadeloupe', 'dial_code' => '+590', 'code' => 'gp'],
            'gu' => ['name' => 'Guam', 'dial_code' => '+1671', 'code' => 'gu'],
            'gt' => ['name' => 'Guatemala', 'dial_code' => '+502', 'code' => 'gt'],
            'gg' => ['name' => 'Guernsey', 'dial_code' => '+44', 'code' => 'gg'],
            'gn' => ['name' => 'Guinea', 'dial_code' => '+224', 'code' => 'gn'],
            'gw' => ['name' => 'Guinea-Bissau', 'dial_code' => '+245', 'code' => 'gw'],
            'gy' => ['name' => 'Guyana', 'dial_code' => '+592', 'code' => 'gy'],
            'ht' => ['name' => 'Haiti', 'dial_code' => '+509', 'code' => 'ht'],
            'hm' => ['name' => 'Heard Island and Mcdonald Islands', 'dial_code' => '+0', 'code' => 'hm'],
            'va' => ['name' => 'Holy See (Vatican City State)', 'dial_code' => '+379', 'code' => 'va'],
            'hn' => ['name' => 'Honduras', 'dial_code' => '+504', 'code' => 'hn'],
            'hk' => ['name' => 'Hong Kong', 'dial_code' => '+852', 'code' => 'hk'],
            'hu' => ['name' => 'Hungary', 'dial_code' => '+36', 'code' => 'hu'],
            'is' => ['name' => 'Iceland', 'dial_code' => '+354', 'code' => 'is'],
            'in' => ['name' => 'India', 'dial_code' => '+91', 'code' => 'in'],
            'id' => ['name' => 'Indonesia', 'dial_code' => '+62', 'code' => 'id'],
            'ir' => ['name' => 'Iran, Islamic Republic of Persian Gulf', 'dial_code' => '+98', 'code' => 'ir'],
            'iq' => ['name' => 'Iraq', 'dial_code' => '+964', 'code' => 'iq'],
            'ie' => ['name' => 'Ireland', 'dial_code' => '+353', 'code' => 'ie'],
            'im' => ['name' => 'Isle of Man', 'dial_code' => '+44', 'code' => 'im'],
            'il' => ['name' => 'Israel', 'dial_code' => '+972', 'code' => 'il'],
            'it' => ['name' => 'Italy', 'dial_code' => '+39', 'code' => 'it'],
            'jm' => ['name' => 'Jamaica', 'dial_code' => '+1876', 'code' => 'jm'],
            'jp' => ['name' => 'Japan', 'dial_code' => '+81', 'code' => 'jp'],
            'je' => ['name' => 'Jersey', 'dial_code' => '+44', 'code' => 'je'],
            'jo' => ['name' => 'Jordan', 'dial_code' => '+962', 'code' => 'jo'],
            'kz' => ['name' => 'Kazakhstan', 'dial_code' => '+7', 'code' => 'kz'],
            'ke' => ['name' => 'Kenya', 'dial_code' => '+254', 'code' => 'ke'],
            'ki' => ['name' => 'Kiribati', 'dial_code' => '+686', 'code' => 'ki'],
            'kp' => ['name' => 'Korea, Democratic People\'s Republic of Korea', 'dial_code' => '+850', 'code' => 'kp'],
            'kr' => ['name' => 'Korea, Republic of South Korea', 'dial_code' => '+82', 'code' => 'kr'],
            'xk' => ['name' => 'Kosovo', 'dial_code' => '+383', 'code' => 'xk'],
            'kw' => ['name' => 'Kuwait', 'dial_code' => '+965', 'code' => 'kw'],
            'kg' => ['name' => 'Kyrgyzstan', 'dial_code' => '+996', 'code' => 'kg'],
            'la' => ['name' => 'Laos', 'dial_code' => '+856', 'code' => 'la'],
            'lv' => ['name' => 'Latvia', 'dial_code' => '+371', 'code' => 'lv'],
            'lb' => ['name' => 'Lebanon', 'dial_code' => '+961', 'code' => 'lb'],
            'ls' => ['name' => 'Lesotho', 'dial_code' => '+266', 'code' => 'ls'],
            'lr' => ['name' => 'Liberia', 'dial_code' => '+231', 'code' => 'lr'],
            'ly' => ['name' => 'Libyan Arab Jamahiriya', 'dial_code' => '+218', 'code' => 'ly'],
            'li' => ['name' => 'Liechtenstein', 'dial_code' => '+423', 'code' => 'li'],
            'lt' => ['name' => 'Lithuania', 'dial_code' => '+370', 'code' => 'lt'],
            'lu' => ['name' => 'Luxembourg', 'dial_code' => '+352', 'code' => 'lu'],
            'mo' => ['name' => 'Macao', 'dial_code' => '+853', 'code' => 'mo'],
            'mk' => ['name' => 'Macedonia', 'dial_code' => '+389', 'code' => 'mk'],
            'mg' => ['name' => 'Madagascar', 'dial_code' => '+261', 'code' => 'mg'],
            'mw' => ['name' => 'Malawi', 'dial_code' => '+265', 'code' => 'mw'],
            'my' => ['name' => 'Malaysia', 'dial_code' => '+60', 'code' => 'my'],
            'mv' => ['name' => 'Maldives', 'dial_code' => '+960', 'code' => 'mv'],
            'ml' => ['name' => 'Mali', 'dial_code' => '+223', 'code' => 'ml'],
            'mt' => ['name' => 'Malta', 'dial_code' => '+356', 'code' => 'mt'],
            'mh' => ['name' => 'Marshall Islands', 'dial_code' => '+692', 'code' => 'mh'],
            'mq' => ['name' => 'Martinique', 'dial_code' => '+596', 'code' => 'mq'],
            'mr' => ['name' => 'Mauritania', 'dial_code' => '+222', 'code' => 'mr'],
            'mu' => ['name' => 'Mauritius', 'dial_code' => '+230', 'code' => 'mu'],
            'yt' => ['name' => 'Mayotte', 'dial_code' => '+262', 'code' => 'yt'],
            'mx' => ['name' => 'Mexico', 'dial_code' => '+52', 'code' => 'mx'],
            'fm' => ['name' => 'Micronesia, Federated States of Micronesia', 'dial_code' => '+691', 'code' => 'fm'],
            'md' => ['name' => 'Moldova', 'dial_code' => '+373', 'code' => 'md'],
            'mc' => ['name' => 'Monaco', 'dial_code' => '+377', 'code' => 'mc'],
            'mn' => ['name' => 'Mongolia', 'dial_code' => '+976', 'code' => 'mn'],
            'me' => ['name' => 'Montenegro', 'dial_code' => '+382', 'code' => 'me'],
            'ms' => ['name' => 'Montserrat', 'dial_code' => '+1664', 'code' => 'ms'],
            'ma' => ['name' => 'Morocco', 'dial_code' => '+212', 'code' => 'ma'],
            'mz' => ['name' => 'Mozambique', 'dial_code' => '+258', 'code' => 'mz'],
            'mm' => ['name' => 'Myanmar', 'dial_code' => '+95', 'code' => 'mm'],
            'na' => ['name' => 'Namibia', 'dial_code' => '+264', 'code' => 'na'],
            'nr' => ['name' => 'Nauru', 'dial_code' => '+674', 'code' => 'nr'],
            'np' => ['name' => 'Nepal', 'dial_code' => '+977', 'code' => 'np'],
            'nl' => ['name' => 'Netherlands', 'dial_code' => '+31', 'code' => 'nl'],
            'an' => ['name' => 'Netherlands Antilles', 'dial_code' => '+599', 'code' => 'an'],
            'nc' => ['name' => 'New Caledonia', 'dial_code' => '+687', 'code' => 'nc'],
            'nz' => ['name' => 'New Zealand', 'dial_code' => '+64', 'code' => 'nz'],
            'ni' => ['name' => 'Nicaragua', 'dial_code' => '+505', 'code' => 'ni'],
            'ne' => ['name' => 'Niger', 'dial_code' => '+227', 'code' => 'ne'],
            'ng' => ['name' => 'Nigeria', 'dial_code' => '+234', 'code' => 'ng'],
            'nu' => ['name' => 'Niue', 'dial_code' => '+683', 'code' => 'nu'],
            'nf' => ['name' => 'Norfolk Island', 'dial_code' => '+672', 'code' => 'nf'],
            'mp' => ['name' => 'Northern Mariana Islands', 'dial_code' => '+1670', 'code' => 'mp'],
            'no' => ['name' => 'Norway', 'dial_code' => '+47', 'code' => 'no'],
            'om' => ['name' => 'Oman', 'dial_code' => '+968', 'code' => 'om'],
            'pk' => ['name' => 'Pakistan', 'dial_code' => '+92', 'code' => 'pk'],
            'pw' => ['name' => 'Palau', 'dial_code' => '+680', 'code' => 'pw'],
            'ps' => ['name' => 'Palestinian Territory, Occupied', 'dial_code' => '+970', 'code' => 'ps'],
            'pa' => ['name' => 'Panama', 'dial_code' => '+507', 'code' => 'pa'],
            'pg' => ['name' => 'Papua New Guinea', 'dial_code' => '+675', 'code' => 'pg'],
            'py' => ['name' => 'Paraguay', 'dial_code' => '+595', 'code' => 'py'],
            'pe' => ['name' => 'Peru', 'dial_code' => '+51', 'code' => 'pe'],
            'ph' => ['name' => 'Philippines', 'dial_code' => '+63', 'code' => 'ph'],
            'pn' => ['name' => 'Pitcairn', 'dial_code' => '+64', 'code' => 'pn'],
            'pl' => ['name' => 'Poland', 'dial_code' => '+48', 'code' => 'pl'],
            'pt' => ['name' => 'Portugal', 'dial_code' => '+351', 'code' => 'pt'],
            'pr' => ['name' => 'Puerto Rico', 'dial_code' => '+1939', 'code' => 'pr'],
            'qa' => ['name' => 'Qatar', 'dial_code' => '+974', 'code' => 'qa'],
            'ro' => ['name' => 'Romania', 'dial_code' => '+40', 'code' => 'ro'],
            'ru' => ['name' => 'Russia', 'dial_code' => '+7', 'code' => 'ru'],
            'rw' => ['name' => 'Rwanda', 'dial_code' => '+250', 'code' => 'rw'],
            're' => ['name' => 'Reunion', 'dial_code' => '+262', 'code' => 're'],
            'bl' => ['name' => 'Saint Barthelemy', 'dial_code' => '+590', 'code' => 'bl'],
            'sh' => ['name' => 'Saint Helena, Ascension and Tristan Da Cunha', 'dial_code' => '+290', 'code' => 'sh'],
            'kn' => ['name' => 'Saint Kitts and Nevis', 'dial_code' => '+1869', 'code' => 'kn'],
            'lc' => ['name' => 'Saint Lucia', 'dial_code' => '+1758', 'code' => 'lc'],
            'mf' => ['name' => 'Saint Martin', 'dial_code' => '+590', 'code' => 'mf'],
            'pm' => ['name' => 'Saint Pierre and Miquelon', 'dial_code' => '+508', 'code' => 'pm'],
            'vc' => ['name' => 'Saint Vincent and the Grenadines', 'dial_code' => '+1784', 'code' => 'vc'],
            'ws' => ['name' => 'Samoa', 'dial_code' => '+685', 'code' => 'ws'],
            'sm' => ['name' => 'San Marino', 'dial_code' => '+378', 'code' => 'sm'],
            'st' => ['name' => 'Sao Tome and Principe', 'dial_code' => '+239', 'code' => 'st'],
            'sa' => ['name' => 'Saudi Arabia', 'dial_code' => '+966', 'code' => 'sa'],
            'sn' => ['name' => 'Senegal', 'dial_code' => '+221', 'code' => 'sn'],
            'rs' => ['name' => 'Serbia', 'dial_code' => '+381', 'code' => 'rs'],
            'sc' => ['name' => 'Seychelles', 'dial_code' => '+248', 'code' => 'sc'],
            'sl' => ['name' => 'Sierra Leone', 'dial_code' => '+232', 'code' => 'sl'],
            'sg' => ['name' => 'Singapore', 'dial_code' => '+65', 'code' => 'sg'],
            'sk' => ['name' => 'Slovakia', 'dial_code' => '+421', 'code' => 'sk'],
            'si' => ['name' => 'Slovenia', 'dial_code' => '+386', 'code' => 'si'],
            'sb' => ['name' => 'Solomon Islands', 'dial_code' => '+677', 'code' => 'sb'],
            'so' => ['name' => 'Somalia', 'dial_code' => '+252', 'code' => 'so'],
            'za' => ['name' => 'South Africa', 'dial_code' => '+27', 'code' => 'za'],
            'ss' => ['name' => 'South Sudan', 'dial_code' => '+211', 'code' => 'ss'],
            'gs' => ['name' => 'South Georgia and the South Sandwich Islands', 'dial_code' => '+500', 'code' => 'gs'],
            'es' => ['name' => 'Spain', 'dial_code' => '+34', 'code' => 'es'],
            'lk' => ['name' => 'Sri Lanka', 'dial_code' => '+94', 'code' => 'lk'],
            'sd' => ['name' => 'Sudan', 'dial_code' => '+249', 'code' => 'sd'],
            'sr' => ['name' => 'Suriname', 'dial_code' => '+597', 'code' => 'sr'],
            'sj' => ['name' => 'Svalbard and Jan Mayen', 'dial_code' => '+47', 'code' => 'sj'],
            'sz' => ['name' => 'Swaziland', 'dial_code' => '+268', 'code' => 'sz'],
            'se' => ['name' => 'Sweden', 'dial_code' => '+46', 'code' => 'se'],
            'ch' => ['name' => 'Switzerland', 'dial_code' => '+41', 'code' => 'ch'],
            'sy' => ['name' => 'Syrian Arab Republic', 'dial_code' => '+963', 'code' => 'sy'],
            'tw' => ['name' => 'Taiwan', 'dial_code' => '+886', 'code' => 'tw'],
            'tj' => ['name' => 'Tajikistan', 'dial_code' => '+992', 'code' => 'tj'],
            'tz' => ['name' => 'Tanzania, United Republic of Tanzania', 'dial_code' => '+255', 'code' => 'tz'],
            'th' => ['name' => 'Thailand', 'dial_code' => '+66', 'code' => 'th'],
            'tl' => ['name' => 'Timor-Leste', 'dial_code' => '+670', 'code' => 'tl'],
            'tg' => ['name' => 'Togo', 'dial_code' => '+228', 'code' => 'tg'],
            'tk' => ['name' => 'Tokelau', 'dial_code' => '+690', 'code' => 'tk'],
            'to' => ['name' => 'Tonga', 'dial_code' => '+676', 'code' => 'to'],
            'tt' => ['name' => 'Trinidad and Tobago', 'dial_code' => '+1868', 'code' => 'tt'],
            'tn' => ['name' => 'Tunisia', 'dial_code' => '+216', 'code' => 'tn'],
            'tr' => ['name' => 'Turkey', 'dial_code' => '+90', 'code' => 'tr'],
            'tm' => ['name' => 'Turkmenistan', 'dial_code' => '+993', 'code' => 'tm'],
            'tc' => ['name' => 'Turks and Caicos Islands', 'dial_code' => '+1649', 'code' => 'tc'],
            'tv' => ['name' => 'Tuvalu', 'dial_code' => '+688', 'code' => 'tv'],
            'ug' => ['name' => 'Uganda', 'dial_code' => '+256', 'code' => 'ug'],
            'ua' => ['name' => 'Ukraine', 'dial_code' => '+380', 'code' => 'ua'],
            'ae' => ['name' => 'United Arab Emirates', 'dial_code' => '+971', 'code' => 'ae'],
            'gb' => ['name' => 'United Kingdom', 'dial_code' => '+44', 'code' => 'gb'],
            'us' => ['name' => 'United States', 'dial_code' => '+1', 'code' => 'us'],
            'uy' => ['name' => 'Uruguay', 'dial_code' => '+598', 'code' => 'uy'],
            'uz' => ['name' => 'Uzbekistan', 'dial_code' => '+998', 'code' => 'uz'],
            'vu' => ['name' => 'Vanuatu', 'dial_code' => '+678', 'code' => 'vu'],
            've' => ['name' => 'Venezuela, Bolivarian Republic of Venezuela', 'dial_code' => '+58', 'code' => 've'],
            'vn' => ['name' => 'Vietnam', 'dial_code' => '+84', 'code' => 'vn'],
            'vg' => ['name' => 'Virgin Islands, British', 'dial_code' => '+1284', 'code' => 'vg'],
            'vi' => ['name' => 'Virgin Islands, U.S.', 'dial_code' => '+1340', 'code' => 'vi'],
            'wf' => ['name' => 'Wallis and Futuna', 'dial_code' => '+681', 'code' => 'wf'],
            'ye' => ['name' => 'Yemen', 'dial_code' => '+967', 'code' => 'ye'],
            'zm' => ['name' => 'Zambia', 'dial_code' => '+260', 'code' => 'zm'],
            'zw' => ['name' => 'Zimbabwe', 'dial_code' => '+263', 'code' => 'zw']
        ];
    }

    /**
     * Return array with available phone codes
     *
     * @return array
     */
    public function phoneCodes()
    {
        return \Arr::pluck(self::phoneAssoc(), 'dial_code');
    }

    /**
     * Should be used for add custom configuration for request
     *
     * @return void
     */
    protected abstract function configureRequest();

    /**
     * Run curl which send sms on API
     *
     * @return bool
     */
    protected function sendSMS()
    {
        $result = curl_exec($this->ch);
        $logEnabled = env('SMS_LOG', false);

        if (curl_errno($this->ch)) {
            if ($logEnabled) {
                Log::channel('sms')->error(curl_error($this->ch), ['phone' => $this->phone]);
            }

            return false;
        }

        if ($logEnabled) {
            Log::channel('sms')->info($result, ['phone' => $this->phone]);
        }

        return true;
    }


    /**
     * Destroy curl resource
     */
    public function __destruct()
    {
        curl_close($this->ch);
    }

}
