<?php
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\RTLBuffer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class EPSON
{
    public static function patientReceiptPrint($reservation, $ipToReception, $printerIp)
    {
        $code = explode('-', $reservation['code']);
        $patient = Patient::getById($reservation['patient_id']);
        $physician = User::getById($reservation['physician_id']);
        $clinic = Clinic::getById($reservation['clinic_id']);
        $userLoginIp = UserLoginIp::check($reservation['physician_id'], null, null, 7);
        $room = 'N/A';
        $screen = 'N/A';
        $screenData = 'N/A';
        if ($userLoginIp) {
            $room = IpToRoom::getAll(array(
                'getFirst' => true,
                'type' => 1, // ip to room
                'ip' => $userLoginIp['ip'],
            ));
            if ($room) {
                $screen = IpToRoom::getAll(array(
                    'getFirst' => true,
                    'room_id' => $room['id'],
                    'type' => 2, // screen to room
                ));
                if ($screen) {
                    $screenData = IpToScreen::getById($screen['ip_to_screen_id']);
                }
            }
        }
        $printer = self::USBPrint($printerIp);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $img = EscposImage::load(public_path('images/sgh-logo.jpg'));
        $printer->graphics($img, 3);
        $printer->setTextSize(4, 4);
        $printer->setEmphasis(true);
        $printer->text("" . $code[0] . '-' . $code[1] . "\n");
        $printer->initialize();
        $printer->feed(1);
        $printer->setTextSize(2, 2);
        $printer->setFont(Printer::FONT_B);
        $printer->text("Arrived At: " . date('h:ia - dMY') . "\n");
        if ($patient['registration_no']) {
            $printer->feed(1);
            $printer->text('PIN: ' . $patient['registration_no'] . "\n");
        }
        $printer->feed(1);
        $printer->text('DR. ' . ucwords(strtolower($physician['first_name'] . ' ' . $physician['last_name'])) . "\n");
        $printer->feed(1);
        $printer->text("Clinic: " . $clinic['name'] . "\n");
        $printer->feed(1);
        $printer->text("Reception No. " . $ipToReception['name'] . "\n");
        $printer->feed(1);
        $printer->text("Waiting Area: " . $screenData['wait_area_name'] . "\n");
        $printer->feed(1);
        $printer->text("Corridor No. " . $room['corridor_num'] . "\n");
        $printer->feed(1);
        $printer->text("Clinic No. " . $room['room_num'] . "\n");
//        $printer->setFont(Printer::FONT_B);
//        $printer->setUnderline(true);
        $printer->cut();

        /* Close printer */
        $printer->close();
    }

    public static function USBPrint($printerIp)
    {
//        $connector = new WindowsPrintConnector($printerName);
//        $printer = new Printer($connector);
        $connector = new NetworkPrintConnector($printerIp, 9100);
        $printer = new Printer($connector);
        return $printer;
    }

    public static function Test()
    {
        try {
            $connector = new NetworkPrintConnector("10.1.10.225", 9100);
            $printer = new Printer($connector);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $img = EscposImage::load(public_path('images/sgh-logo.jpg'));
            $printer->graphics($img, 3);
            $printer->setTextSize(4, 4);
            $printer->setEmphasis(true);
            $printer->text("Hi Hossam\n");
            $printer->cut();

            /* Close printer */
            $printer->close();
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}