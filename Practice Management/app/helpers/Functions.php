<?php

class Functions
{
    public static function getAge($yearOfBirthday)
    {
        $currentYear = date('Y');
        if ($yearOfBirthday > $currentYear)
            return 1;
        else
            return $currentYear - $yearOfBirthday;
    }

    public static function getAgeDetails($birthday)
    {
        $date = new DateTime($birthday);
        $now = new DateTime();
        return $now->diff($date);
    }

    public static function get_words($text, $limit = 3)
    {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }

    public static function num_words($text, $limit = 4)
    {
        $words = explode(' ', $text);
        if (count($words) > $limit) {
            $text = '';
            for ($i = 0; $i < $limit; $i++) {
                if(isset($words[$i])) {
                    $text .= $words[$i] . ' ';
                }
            }
        }
        return $text;
    }

    public static function sendSMS($mobile, $body)
    {
        $url = 'http://my.sms40.com/api2.php';
        $method = 'GET';
        $data = [
            'type' => 'SendSMS',
            'username' => 'sghgroup',
            'password' => '4764',
            'sender' => 'SGH Riyadh',
            'mobile' => trim($mobile),
            'message' => $body,
        ];
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);
        return trim($result);
    }

    public static function make3D($num)
    {
        $digits = strlen($num);
        if ($digits == 1) {
            return '00' . $num;
        } elseif ($digits == 2) {
            return '0' . $num;
        } else {
            return $num;
        }
    }

    public static function make2D($num)
    {
        $digits = strlen($num);
        if ($digits == 1) {
            return '0' . $num;
        } else {
            return $num;
        }
    }

    public static function convertToHoursMins($time)
    {
        if ($time < 1) {
            return '00:00';
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return self::make2D($hours) . ':' . self::make2D($minutes);
    }

    public static function GetClientIp()
    {
        return getenv('HTTP_CLIENT_IP') ?:
            getenv('HTTP_X_FORWARDED_FOR') ?:
                getenv('HTTP_X_FORWARDED') ?:
                    getenv('HTTP_FORWARDED_FOR') ?:
                        getenv('HTTP_FORWARDED') ?:
                            getenv('REMOTE_ADDR');
    }

    public static function hoursToSeconds($hour)
    { // $hour must be a string type: "HH:mm:ss"

        $parse = array();
        if (!preg_match('#^(?<hours>[\d]{2}):(?<mins>[\d]{2}):(?<secs>[\d]{2})$#', $hour, $parse)) {
            return 'error';
        }
        return (int)$parse['hours'] * 3600 + (int)$parse['mins'] * 60 + (int)$parse['secs'];

    }

    public static function timeFromSeconds($seconds)
    {
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds - ($h * 3600) - ($m * 60);
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    public static function enNumToAr($word)
    {
        $western_arabic = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'AM', 'PM');
        $eastern_arabic = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩', 'ص', 'م');
        $word = str_replace($western_arabic, $eastern_arabic, $word);

        $dateEnglish = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        $dateArabic = array('يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر');
        return str_replace($dateEnglish, $dateArabic, $word);
    }

    public static function fixDate($date)
    {
        try {
            if ($date) {
                $dateParts = explode('-', $date);
                if ($dateParts[0] < 1200) { // year
                    $dateParts[0] = date('Y');
                }
                if ($dateParts[1] == '0' || $dateParts[1] == '00' || $dateParts[1] > 12) { // month
                    $dateParts[1] = date('m');
                }
                if ($dateParts[2] == '0' || $dateParts[2] == '00' || $dateParts[2] > 31) { // day
                    $dateParts[2] = date('d');
                }
                return implode('-', $dateParts);
            } else {
                return date('Y-m-d');
            }
        } catch (Exception $e) {
            return date('Y-m-d');
        }
    }
}