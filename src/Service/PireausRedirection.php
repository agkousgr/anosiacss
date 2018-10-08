<?php


namespace App\Service;

use App\Entity\IssueNewTicket;

class PireausRedirection
{
    /**
     * @var array
     */
    private $bank_config;

    public function __construct()
    {
        $this->bank_config['AcquirerId'] = 14;
        $this->bank_config['MerchantId'] = 2137477493;
        $this->bank_config['PosId'] = 2141384532;
        $this->bank_config['User'] = 'AN895032';
        $this->bank_config['Password'] = 'YC4589964';

    }

    public function submitOrderToPireaus($checkout)
    {
        $wsdl_uri = 'https://paycenter.piraeusbank.gr/services/tickets/issuer.asmx?WSDL';
        $arrContextOptions = array(
            "ssl"=>array(
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true,
            )
        );
        try {
            $ticket_issuer = new \SoapClient($wsdl_uri, array(
                "exceptions" => 1,
                "soap_version" => SOAP_1_2,
                "stream_context" => stream_context_create($arrContextOptions),
            ));
            $params = array(
                'AcquirerId' => $this->bank_config['AcquirerId'],
                'MerchantId' => $this->bank_config['MerchantId'],
                'PosId' => $this->bank_config['PosId'],
                'Username' => $this->bank_config['User'],
                'Password' => hash('md5', $this->bank_config['Password']),
                'RequestType' => '02',
                'CurrencyCode' => 978,
                'MerchantReference' => $checkout->getOrderNo(),
                'Amount' => $checkout->getTotalOrderCost(),
                'Installments' => pack('H', $checkout->getInstallments()),
                'ExpirePreauth' => 0x1e,
                'Bnpl' => 0x00,
                'Parameters' => 'Anosia'
            );
            dump($params);
            $result = $ticket_issuer->IssueNewTicket(array('Request' => $params));

            if (!$result) {
                $checkout->getPireausResultCode(0);
                return;
            }
            dump($result);
            $checkout->setPireausResultCode($result->IssueNewTicketResult->ResultCode);


            $this->initializeResultCode($checkout);

            return $checkout;
        } catch (SoapFault $sf) {
            return $sf;
        }

    }

    private function initializeResultCode($checkout)
    {
        switch ($checkout->getPireausResultCode()) {
            case '1':
                $checkout->setPireausResultDescription('Υπάρχει πρόβλημα με το Paycenter της τράπεζας Πειραιώς.');
                $checkout->setPireausResultAction('Παρακαλώ δοκιμάστε αργότερα ή επιλέξτε διαφορετικό τύπο πληρωμής.');
                break;
            case (preg_match('/50.*/', $checkout->getPireausResultCode()) ? true : false):
                $checkout->setPireausResultDescription('Πρόβλημα επικοινωνίας με το σύστημα επεξεργασίας των συναλλαγών.');
                $checkout->setPireausResultAction('Παρακαλώ δοκιμάστε αργότερα ή επιλέξτε διαφορετικό τύπο πληρωμής.');
                break;
            case '981':
                $checkout->setPireausResultDescription('Εισαγωγή λανθασμένων στοιχείων κάρτας (π.χ. λάθος αριθμός, λάθος τύπος κάρτας παρελθοντική ημ/νία λήξης) ή κάρτας που δεν υποστηρίζεται από το σύστημα.');
                $checkout->setPireausResultAction('Να συμπληρωθούν σωστά τα στοιχεία της
κάρτας.');
                break;
            case '1006':
                $checkout->setPireausResultDescription('Η κάρτα σας δε συμμετέχει στο πρόγραμμα των άτοκων δόσεων της Τράπεζας Πειραιώς.');
                $checkout->setPireausResultAction('Να γίνει επικοινωνία με την Τράπεζα
Πειραιώς.');
                break;
            case '1007':
                $checkout->setPireausResultDescription('Το request περιλαμβάνει δόσεις
αλλά δεν υποστηρίζονται στη συγκεκριμένη επιχείρηση.');
                $checkout->setPireausResultAction('Να χρησιμοποιηθεί άλλη κάρτα ή να
επαναληφθεί η συναλλαγή χωρίς δόσεις.');
                break;
            case '1019':
                $checkout->setPireausResultDescription('Το πλήθος δόσεων που χρησιμοποιήθηκε είναι μεγαλύτερο από το μέγιστο επιτρεπτό για τη συγκεκριμένη επιχείρηση.');
                $checkout->setPireausResultAction('Να χρησιμοποιηθεί μικρότερο πλήθος δόσεων.');
                break;
            case '1026':
                $checkout->setPireausResultDescription('Το request περιλαμβάνει δόσεις
αλλά δεν υποστηρίζονται στη συγκεκριμένη επιχείρηση.');
                $checkout->setPireausResultAction('Να πραγματοποιηθεί επικοινωνία με την Τράπεζα Πειραιώς ώστε να ενεργοποιηθούν οι δόσεις.');
                break;
            case '1034':
                $checkout->setPireausResultDescription('Η συναλλαγή στάλθηκε με μη
υποστηριζόμενο τύπο κάρτας.');
                $checkout->setPireausResultAction('Να γίνει επικοινωνία με την Τράπεζα
Πειραιώς.');
                break;
            case '1041':
                $checkout->setPireausResultDescription('Η συναλλαγή στάλθηκε από IP
address η οποία δεν είναι έγκυρη.');
                $checkout->setPireausResultAction('Παρακαλούμε δοκιμάστε άλλο τρόπο πληρωμής ή επικοινείστε μαζί μας.');
                break;
            case '7001':
                $checkout->setPireausResultDescription('Η συναλλαγή στάλθηκε με μη
υποστηριζόμενο τύπο κάρτας.');
                $checkout->setPireausResultAction('Να χρησιμοποιηθεί άλλη κάρτα ή να
επαναληφθεί η συναλλαγή χωρίς δόσεις.');
                break;
            default:
                $checkout->setPireausResultDescription('Παρουσιάστηκε σφάλμα. Κωδικός σφάλματος: ' . $checkout->getPireausResultCode());
                $checkout->setPireausResultAction('Παρακαλούμε δοκιμάστε άλλο τρόπο πληρωμής ή επικοινείστε μαζί μας.');
        }

        return $checkout;
    }
}