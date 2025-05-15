<?php
defined("BASEPATH") or exit("No direct script access allowed");

require "vendor/autoload.php";

use NcJoes\OfficeConverter\OfficeConverter;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\Element\TextRun;
use Google\Client;

class Document extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        $this->load->model("mod_user");
        $this->load->model("mod_division");
        $this->load->model("mod_doctype");
        $this->load->model("mod_docstatus");
        $this->load->model("mod_document_his");
        $this->load->model("mod_document_item");
        $this->load->model("mod_file_document");
        $this->load->model("mod_transfer_document");
    }

    public function index($type)
    {
        $data["content"] = "document/index";
        $data["title"] = "DOKUMEN " . strtoupper($type);
        $data["documents"] = $this->mod_document->get_document("%", $type, $_SESSION['user']['division_id'], $_SESSION['company_id'] ?? "%");
        $data["type"] = $type;
        $this->load->view("layout", $data);
    }

    public function form($type, $id)
    {
        if (!isset($_SESSION['user'])) {
            $this->session->set_flashdata('errmsg', 'Perhatian! Harap login menggunakan username dan password Anda.');
            redirect('login');
        }

        $data["content"] = "document/form";
        $data["title"] = "DOKUMEN " . strtoupper($type);
        $data["document"] = $this->mod_document->get_document($id, $type)[0] ?? [];
        $data["divisions"] = $this->mod_division->get_division();
        $data["doctypes"] = $this->mod_doctype->get_doctype();
        $data["docstatus"] = $this->mod_docstatus->get_docstatus('%', $_SESSION['user']['division_id'], '%' . $type . '%', '%' . $_SESSION['user']['role'] . '%');
        $data["history"] = $this->mod_document_his->get_document_his($id ?? '') ?? [];

        if ($type != "all") {
            $data["item"] = $this->mod_document_item->get_document_item($id, $type);
            array_push($data["item"], []);
            $data["dfile"] = $this->mod_file_document->get_file_document($id);
            array_push($data["dfile"], []);
            $data["transfer"] = $this->mod_transfer_document->get_transfer_document($id)[0] ?? [];
        }

        $data["type"] = $type;
        $this->load->view("layout", $data);
    }

    public function store_transfer()
    {
        $_SESSION['old'] = $_POST;

        $this->db->trans_begin();

        try {
            $data = array(
                "document_id"               => $_POST['mt_document_id'] ?? '',
                "transfer_method"           => $_POST["mt_transfer_method"] ?? '',
                "transfer_bank"             => $_POST["mt_transfer_bank"] ?? '',
                "transfer_account"          => $_POST["mt_transfer_account"] ?? '',
                "transfer_account_name"     => $_POST["mt_transfer_account_name"] ?? '',
                "transfer_amount"           => str_replace(",", "", $_POST["mt_transfer_amount"] ?? ''),
                "note"                      => $_POST["mt_note"] ?? '',
                "to_division_id"            => $_POST['mt_to_division_id'] ?? ''
            );
            if (move_uploaded_file($_FILES["mt_file"]["tmp_name"], "_shared/upload/" . $_FILES["mt_file"]["name"])) {
                $data['file']             = "_shared/upload/" . $_FILES["mt_file"]["name"];
                $data["transfer_date"]    = date('Y-m-d H:i:s');
            } else {
                $data['file'] = $_POST['mt_file_'] ?? '';
            }
            if (!empty($_POST['mt_id'])) {
                $this->mod_transfer_document->set_transfer_document($_POST['mt_id'], $data);
            } else {
                $data["from_division_id"] = $_SESSION['user']['division_id'] ?? '';

                $this->mod_transfer_document->add_transfer_document($data);
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
        }
        $this->db->trans_commit();

        $_SESSION['old'] = null;

        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function store($type)
    {
        $_SESSION['old'] = $_POST;

        $this->db->trans_begin();

        try {
            $id = $_POST["id"];
            $data = array(
                "document_number"   => strtoupper($_POST["document_number"]),
                "subject"           => $_POST["subject"] ?? $this->mod_doctype->get_doctype($type)[0]["name"] ?? '',
                "content"           => $_POST["content"] ?? $this->mod_doctype->get_doctype($type)[0]["name"] ?? '',
                "doctype_id"        => $_POST["doctype_id"] ?? $this->mod_doctype->get_doctype($type)[0]["id"] ?? '',
                "company_id"        => $_SESSION['company_id'] ?? '',
                "from_division_id"  => $_POST["from_division_id"] ?? '',
                "note"              => $_POST["note"] ?? '',
                "user_create"       => $_POST["user_create"] ?? '',
                "release_date"      => $_POST["release_date"] ?? '',
                "create_date"       => $_POST["create_date"] ?? '',
                "status"            => $_POST["status"] ?? '',
            );
            $document_id = $this->do_store($id, "all", $data);
            if ($type == "ca" || $type == "pc" || $type == "pp") {
                $transfer_date = $this->store_file($document_id);

                $data = array(
                    "document_id"           => $document_id,
                    "project"               => $_POST["project"] ?? '',
                    "transfer_date"         => $transfer_date ?? '',
                    "transfer_method"       => $_POST["transfer_method"] ?? '',
                    "transfer_bank"         => $_POST["transfer_bank"] ?? '',
                    "transfer_account"      => $_POST["transfer_account"] ?? '',
                    "transfer_account_name" => $_POST["transfer_account_name"] ?? '',
                    "transfer_amount"       => str_replace(",", "", $_POST["transfer_amount"] ?? ''),
                );
                if ($type == "ca") {
                    $data["leave_date"] = $_POST["leave_date"] ?? '';
                    $data["back_date"]  = $_POST["back_date"] ?? '';
                } else if ($type == "pp") {
                    $data["buy_type"]   = $_POST["buy_type"] ?? '';
                    $data["buy_note"]   = $_POST["buy_note"] ?? '';
                }

                $this->do_store($id, $type, $data);
                $this->store_item($type, $data);
            }

            if ($_POST["document_number"] == null) {
                $data = array(
                    "document_id"       => $document_id,
                    "status"            => $_POST["status"]
                );
                $this->do_store($document_id, "all", $data);
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
        }
        $this->db->trans_commit();

        $_SESSION['old'] = null;

        return redirect("document/form/$type/$document_id");
    }

    public function store_file($document_id)
    {
        $isfile = false;
        $this->mod_file_document->remove_file_document($document_id ?? '');
        for ($i = 0; $i < count($_POST['note_f'] ?? []); $i++) {
            if ($_POST['note_f'][$i] != "") {
                $data = array(
                    'document_id'     => $document_id,
                    'note'            => $_POST['note_f'][$i]
                );

                if (move_uploaded_file($_FILES["file_f"]["tmp_name"][$i], "_shared/upload/" . $_FILES["file_f"]["name"][$i])) {
                    $data['file']  = "_shared/upload/" . $_FILES["file_f"]["name"][$i];
                } else {
                    $data['file'] = $_POST['file_f_'][$i];
                }

                $this->mod_file_document->add_file_document($data);
                $isfile = true;
            }
        }
        if ($isfile) {
            return date('Y-m-d H:i:s');
        } else {
            return null;
        }
    }

    public function store_item($type, $data)
    {
        if ($type == "ca" || $type == "pc" || $type == "pp") {
            $this->mod_document_item->remove_document_item($data['document_id'] ?? '', $type);
            for ($i = 0; $i < count($_POST['description'] ?? []); $i++) {
                if ($_POST['description'][$i] ?? '' != "") {
                    $data = array(
                        'document_id'     => $data['document_id'],
                        'description'     => $_POST['description'][$i] ?? '',
                        'unit'            => $_POST['unit'][$i],
                        'price'           => str_replace(",", "", $_POST['price'][$i])
                    );

                    if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], "_shared/upload/" . $_FILES["file"]["name"][$i])) {
                        $data['file']  = "_shared/upload/" . $_FILES["file"]["name"][$i];
                    } else {
                        $data['file'] = $_POST['file_'][$i];
                    }

                    $this->mod_document_item->add_document_item($type, $data);
                }
            }
        }
    }

    public function do_store($id, $type, $data)
    {
        $document_number = "";
        if ($type != "all") {
            $id = $this->mod_document->get_document($id, $type)[0]["id_2"] ?? "";
        }
        if ($id != "") {
            $this->mod_document->set_document($id, $type, $data);
        } else {
            if ($type == "all") {
                $company_code = $this->mod_company->get_company($data["company_id"] ?? "")[0]["code"] ?? "GTI";
                $division_code = $this->mod_division->get_division($data["from_division_id"] ?? "")[0]["code"] ?? "";
                $doctype_code = $this->mod_doctype->get_doctype($data["doctype_id"] ?? $type)[0]["code"];

                $document_number = $doctype_code . "/" . $division_code . "/" . $company_code . "/" . number_to_roman(date("m")) . "/" . date("Y");
                $document_number = $this->mod_document->get_document_number("%" . $document_number)[0]["last_document_number"] . "/" . $document_number;

                $document_number = strtoupper($document_number);

                $data["document_number"]  = $document_number;
                $data['user_create_id'] = $_SESSION['user']['id'];
            }
            $id = $this->mod_document->add_document($type, $data);
        }

        if ($type == "all") {
            $data = array(
                "document_id"      => $id,
                "update_date"      => date("Y-m-d H:i:s"),
                "user_update"      => $_SESSION['user']['fullname'],
                "status_update"    => $_POST["status"],
                "note"             => $_POST["note"],
                "from_division_id" => $_SESSION['user']['division_id'],
            );
            $this->mod_document_his->add_document_his($data);

            $division_id = $this->mod_docstatus->get_docstatus($_POST["status"]);
            $division_id = !empty($division_id) ? $division_id[0]['to_division_id'] : "0";
            $division_id = $division_id != '0' ? $division_id : $_SESSION['user']['division_id'];
            $notification = array(
                "title"     => 'PEMBERITAHUAN! TERDAPAT 1 DOKUMEN BARU DENGAN NOMOR ' . (!empty($_POST["document_number"] ?? "") ? $_POST["document_number"] : $document_number),
                "body"  =>  $_SESSION['user']['username'] . " : HARAP DAPAT SEGERA MELAKUKAN REVIEW TERHADAP DOKUMEN. " . $_POST["note"],
                "to_division_id" => $division_id,
                "notif_date" => date("Y-m-d H:i:s"),
            );
            $this->mod_notification->add_notification($notification);

            foreach ($this->mod_user->get_user('%', $division_id) as $row) {
                $this->send_notif($row["fcm_token"], $notification['title'], $notification['body']);
            }
        }

        return $id;
    }

    public function cetak($type, $id)
    {
        $data = $this->mod_document->get_document($id, $type)[0];

        $data_ = array(
            "document_id"     => $id,
            "update_date"     => date("Y-m-d H:i:s"),
            "user_update"     => $_SESSION['user']['fullname'],
            "status_update"   => "PRT",
            "note"            => "PRINT",
        );
        $this->mod_document_his->add_document_his($data_);
        $data_ = array(
            "print"          => $data["print"] + 1
        );
        $this->mod_document->set_document($id, "all", $data_);

        $template = "_shared/template/" . strtolower($data["doctype_code"]) . "_" . strtolower($data["company_code"]) . ".docx";
        if (!file_exists($template)) {
            $template = "_shared/template/" . strtolower($type) . "_" . strtolower($data["company_code"]) . ".docx";
        }

        if (!file_exists($template)) {
            $this->session->set_flashdata("errmsg", "Template dokumen tidak ditemukan. Silahkan hubungi administrator.");
            return redirect("document/form/$type/$id");
        }

        $filename = "_shared/surat/";
        $filename .= str_replace('/', '-', $data["doctype_name"]) . "-" . $data["company_code"] . "-" . str_replace("/", "-", $data["document_number"]) . "-" . date("YmdHis");

        $data_ = array(
            "document_number" => $data["document_number"],
            "subject" => $data["subject"],
            "user_create" => $data["user_create"],
            "from_division" => $data["from_division"],
            "to_division" => $data["to_division"],
            "html_content" => $data["content"],
            "release_date" => custom_date_format($data["release_date"], 'd F Y')
        );

        if ($type == "ca" || $type == "pc" || $type == "pp") {
            $data_his_pd = $this->mod_document_his->get_document_his($id, 'PAC');
            $data_his_ap = $this->mod_document_his->get_document_his($id, 'AAC');
            $data_his_ac = $this->mod_document_his->get_document_his($id, 'ACD');
            $data_his_dr = $this->mod_document_his->get_document_his($id, 'ADF');
            $data_his_fn = $this->mod_document_his->get_document_his($id, 'AFA');

            $data_["document_number"] = $data["document_number"];
            $data_["user_create"] = $data["user_create"];
            $data_["from_division"] = $data["from_division"];
            $data_["create_date"] = custom_date_format($data["create_date"], 'd F Y');
            $data_["user_pend"] = '[' . ($data_his_pd[0]['user_update'] ?? '') . ']';
            $data_["pend_date"] = '[' . ((!empty($data_his_pd) ? custom_date_format($data_his_pd[0]['update_date'], 'd/m/Y H:i:s') : '')) . ']';
            $data_["user_app"] = '[' . ($data_his_ap[0]['user_update'] ?? '') . ']';
            $data_["app_date"] = '[' . ((!empty($data_his_ap) ? custom_date_format($data_his_ap[0]['update_date'], 'd/m/Y H:i:s') : '')) . ']';
            $data_["user_acc"] = '[' . ($data_his_ac[0]['user_update'] ?? '') . ']';
            $data_["acc_date"] = '[' . ((!empty($data_his_ac) ? custom_date_format($data_his_ac[0]['update_date'], 'd/m/Y H:i:s') : '')) . ']';
            $data_["user_dir"] = '[' . ($data_his_dr[0]['user_update'] ?? '') . ']';
            $data_["dir_date"] = '[' . ((!empty($data_his_dr) ? custom_date_format($data_his_dr[0]['update_date'], 'd/m/Y H:i:s') : '')) . ']';
            $data_["user_fin"] = '[' . ($data_his_fn[0]['user_update'] ?? '') . ']';
            $data_["fin_date"] = '[' . ((!empty($data_his_fn) ? custom_date_format($data_his_fn[0]['update_date'], 'd/m/Y H:i:s') : '')) . ']';

            $data_['tujuan'] = $data["project"];

            if ($type == "ca" || $type == "pc") {
                $data_["table_item"] = array();
                $data_["image_item"] = array();
                $data_["table_item"][] = array("No", "Keterangan Penggunaan", "Jumlah");
                $no = 1;
                $total = 0;
                foreach ($this->mod_document_item->get_document_item($id, $type) as $row) {
                    if ($row["price"] == '0') continue;
                    $subtotal = (int) $row["unit"] * (int) $row["price"];
                    $total += $subtotal;
                    array_push($data_["table_item"], array(
                        $no++,
                        $row["description"] . " " . $row["unit"] . "x @" . number_format($row["price"]),
                        number_format($subtotal)
                    ));
                    array_push($data_["image_item"], $row["file"] ?? null);
                }
                array_push($data_["table_item"], array("", "Total", number_format($total)));
                array_push($data_["table_item"], array("", "Jumlah yang diterima", number_format($data['transfer_amount'])));
                $data_["transfer_date"] = str_contains($data["transfer_date"], '0000') ? custom_date_format($data["transfer_date"] ?? '', 'd F Y') : '';
            }

            if ($type == 'pp') {
                $total = 0;
                $data_["image_item"] = array();
                foreach ($this->mod_document_item->get_document_item($id, $type) as $row) {
                    if ($row["price"] == '0') continue;
                    $subtotal = (int) $row["unit"] * (int) $row["price"];
                    $total += $subtotal;
                    array_push($data_["image_item"], $row["file"] ?? null);
                }
                $data_['transfer_amount'] = number_format($total);
                $data_['transfer_amount_text'] = terbilang($total) . "rupiah";
                $data_['transfer_method'] = ($data["transfer_method"] ?? '') == 'B' ? 'TRANSFER' : 'TUNAI';
                $data_['transfer_account'] = $data["transfer_account"] ?? '';
                $data_['transfer_bank'] = $data["transfer_bank"] ?? '';
                $data_['transfer_account_name'] = ' atas nama ' . ($data["transfer_account_name"] ?? '');
                $data_["transfer_date"] = str_contains($data["transfer_date"], '0000') ? custom_date_format($data["transfer_date"] ?? '', 'd F Y') : '';
            }
        }

        $data_["print_date"] = custom_date_format(date("Y-m-d H:i:s"), 'd F Y H:i:s') . ' . ' . $data["user_create"] . ' . CETAKAN KE-' . (($data["print"] ?? 0) + 1);

        $this->edit_word($template, $data_, $filename . ".docx");
        $this->word_to_pdf($filename);
    }

    public function delete($type, $id)
    {
        if ($type != "all") {
            $id = $this->mod_document->get_document($id, $type)[0]["id_2"] ?? "";
        }
        $this->mod_document->remove_document($id, $type);
        return redirect("document/index/$type");
    }

    function edit_word($template, $data, $filename)
    {
        $phpWord = new PhpWord();
        $templateProcessor = new TemplateProcessor($template);
        $jctable = new JcTable();
        $tableStyle = new TblWidth();

        $tableStyleArray = [
            'borderSize' => 10,
            'borderColor' => '999999',
            'alignment' => $jctable::CENTER,
            'unit' => $tableStyle::PERCENT,
            'width' => 5000,
        ];

        $trStyle = ['align' => 'center'];
        $thStyle = ['size' => 11, 'bold' => true];
        $tdStyle = ['size' => 11];

        $hasHtml = false;

        foreach ($data as $key => $value) {
            if (str_contains($key, 'table')) {
                $section = $phpWord->addSection();
                $table = $section->addTable($tableStyleArray);

                $tno = 0;
                foreach ($value as $tr) {
                    $table->addRow();
                    foreach ($tr as $td) {
                        $table->addCell(2000, $trStyle)->addText($td, $tno == 0 ? $thStyle : $tdStyle);
                    }
                    $tno++;
                }

                $templateProcessor->setComplexBlock($key, $table);
            } else if (str_contains($key, 'html')) {
                $hasHtml = true;
                $html = $this->clean_html($value);

                $phpWord2 = new PhpWord();
                $section2 = $phpWord2->addSection();
                Html::addHtml($section2, $html, false, false);
                $elements = $section2->getElements();

                $section = $phpWord->addSection();
                foreach ($elements as $i => $element) {
                    $section->addText('${' . $key . '_' . $i . '}');
                }

                $templateProcessor->setComplexBlock($key, $section);
            } else if (str_contains($key, 'image')) {
                $section = $phpWord->addSection();
                foreach ($value as $i => $element) {
                    if ($element != null) {
                        $section->addText('${' . $key . '_' . $i . '}');
                    }
                }

                $templateProcessor->setComplexBlock($key, $section);
            } else {
                $templateProcessor->setValue($key, htmlspecialchars($value));
            }
        }

        if ($hasHtml) {
            $tempFile = $template . '_temp.docx';
            $templateProcessor->saveAs($tempFile);
            $templateProcessor2 = new TemplateProcessor($tempFile);

            foreach ($data as $key => $value) {
                if (str_contains($key, 'html')) {
                    $html = $this->clean_html($value);

                    $phpWord2 = new PhpWord();
                    $section2 = $phpWord2->addSection();
                    Html::addHtml($section2, $html, false, false);
                    $elements = $section2->getElements();

                    foreach ($elements as $i => $element) {
                        if ($element instanceof \PhpOffice\PhpWord\Element\Image) {
                            $templateProcessor2->setImageValue($key . '_' . $i, [
                                'path' => $element->getSource(),
                                'width' => 500,
                                'height' => 500,
                            ]);
                        } else {
                            $templateProcessor2->setComplexBlock($key . '_' . $i, $element);
                        }
                    }
                } else if (str_contains($key, 'image')) {
                    foreach ($value as $i => $element) {
                        if ($element != null) {
                            $element = str_replace(base_url(), "", $element);
                            $templateProcessor2->setImageValue($key . '_' . $i, [
                                'path' => $element,
                                'width' => 300,
                                'height' => 300,
                            ]);
                        }
                    }
                }
            }

            $templateProcessor2->saveAs($filename);
        } else {
            $templateProcessor->saveAs($filename);
        }
    }

    function base64_to_temp_image(string $base64, string $ext = 'jpg'): string
    {
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
        $filePath = '_shared/tmp/img_' . uniqid() . '.' . $ext;
        if (file_put_contents($filePath, $data)) {
            return $filePath;
        } else {
            throw new Exception("Failed to write the image to temp file.");
        }
    }

    function clean_html($html)
    {
        $html = preg_replace('/<br([^>]*)>/', '<br $1/>', $html);
        $html = preg_replace('/<hr([^>]*)>/', '<hr $1/>', $html);
        $html = preg_replace('/<img([^>]*)>/', '<img $1/>', $html);
        $html = preg_replace('/<table([^>]*)>/', '<table border="1" style="width:100%" $1>', $html);

        if (preg_match_all('/<img[^>]+src="data:image\/([^;]+);base64,([^"]+)"[^>]*>/i', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $ext = $match[1];
                $base64 = 'data:image/' . $match[1] . ';base64,' . $match[2];
                $tempFile = base_url() . $this->base64_to_temp_image($base64, $ext);
                $html = str_replace($match[0], '<img src="' . $tempFile . '"/>', $html);
            }
        }

        $html = str_replace('<p>', '', $html);
        $html = str_replace('</p>', '', $html);

        return $html;
    }

    function word_to_pdf($filename)
    {
        echo "<html><head>";
        echo '<link rel="shortcut icon" href="https://fantech.id/wp-content/uploads/2023/06/Fantech-Indonesia-2.png" />';
        echo "<title>" . str_replace('_', ' ', strtoupper(substr($filename, strrpos($filename, '/') + 1))) . "</title>";
        echo "</head><body style='margin:0px;padding:0px;overflow:hidden;'>";
        // 		echo '<iframe src="' . base_url() .  "cetak/show_word?file=" . $filename . ".docx" . '" style="width:100%;height:100%;"></iframe>';
        echo "<iframe src='https://view.officeapps.live.com/op/embed.aspx?src=" . base_url() . $filename . ".docx" . "' width='100%' height='100%'></iframe>";
        // 		echo '<iframe src="https://docs.google.com/gview?url=' . base_url() . $filename . ".docx" . '&embedded=true" width="100%" height="100%"></iframe>';
        echo "</body></html>";
    }

    function send_email($to, $subject, $body)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->IsSMTP();
            $mail->CharSet = "UTF-8";

            $mail->Host       = "mawarserver.ardetamedia.net";
            $mail->SMTPDebug  = 0;
            $mail->SMTPAuth   = true;
            $mail->Port       = 587;
            $mail->Username   = "yohanes.randy@corsys.co.id";
            $mail->Password   = "Kurnianto72639!!!";
            $mail->setFrom("yohanes.randy@corsys.co.id");

            $mail->addAddress($to);
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            // $mail->AltBody = $body;

            $mail->send();
        } catch (Exception $e) {
            $this->session->set_flashdata("errmsg", "Email gagal dikirim. Pesan error: $mail->ErrorInfo | " . $e->getMessage());
        }
    }

    function send_notif($to, $title, $body)
    {
        $client = new Client();
        $client->setAuthConfig('yortech-id-firebase-adminsdk-fbsvc-271ff775bd.json');
        $client->addScope('https://www.googleapis.com/auth/cloud-platform');
        $accessToken = $client->fetchAccessTokenWithAssertion();
        $accessToken = $accessToken['access_token'] ?? null;

        $projectId = 'yortech-id';
        $fcmUrl = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

        $data = [
            'message' => [
                'token' => $to,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ]
        ];

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json; UTF-8',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        echo $response;
    }
}
