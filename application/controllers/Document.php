<?php
defined("BASEPATH") or exit("No direct script access allowed");

require "vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Document extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        $this->load->model("mod_user");
        $this->load->model("mod_document");
        $this->load->model("mod_company");
        $this->load->model("mod_division");
        $this->load->model("mod_document_type");
        $this->load->model("mod_document_his");
        $this->load->model("mod_document_item");

        $this->load->library("pdf");
    }

    public function index($type)
    {
        $data["content"] = "document/index";
        $data["title"] = "DOKUMEN " . strtoupper($type);
        $data["data"] = $this->mod_document->get_document("%", $type);
        $data["type"] = $type;
        $this->load->view("layout", $data);
    }

    public function form($type, $id)
    {
        $data["content"] = "document/form";
        $data["title"] = "DOKUMEN " . strtoupper($type);
        $data["data"] = $this->mod_document->get_document($id, $type)[0] ?? [];
        if ($type != "all") {
            $data["history"] = $this->mod_document_his->get_document_his($id ?? '') ?? [];
            $data["item"] = $this->mod_document_item->get_document_item($id, $type);
            array_push($data["item"], []);
        }


        $data["type"] = $type;
        $this->load->view("layout", $data);
    }

    public function store($type)
    {
        $id = $_POST["id"];
        $data = array(
            "document_number"   => strtoupper($_POST["document_number"]),
            "subject"           => $_POST["subject"] ?? $this->mod_document_type->find_document_type_by_id($type)[0]["name"],
            "content"           => $_POST["content"] ?? $this->mod_document_type->find_document_type_by_id($type)[0]["name"],
            "document_type_id"  => $_POST["document_type_id"] ?? $this->mod_document_type->find_document_type_by_id($type)[0]["id"],
            "company_id"        => $_POST["company_id"] ?? 1,
            "from_division_id"  => $_POST["from_division_id"],
            "to_division_id"    => $_POST["to_division_id"],
            "user_create"       => $_POST["user_create"],
            "release_date"      => $_POST["release_date"],
            "create_date"       => $_POST["create_date"],
            "status"            => $_POST["status"],
        );
        $document_id = $this->do_store($id, "all", $data);
        if ($type != "all") {
            if ($type == "ca") {
                $data = array(
                    "document_id"        => $document_id,
                    "project"            => $_POST["project"],
                    "client"             => $_POST["client"],
                    "note"               => $_POST["note"],
                    "leave_date"         => $_POST["leave_date"],
                    "back_date"          => $_POST["back_date"],
                    "transfer_date"      => $_POST["transfer_date"],
                    "transfer_method"    => $_POST["transfer_method"],
                    "transfer_bank"      => $_POST["transfer_bank"],
                    "transfer_account"   => $_POST["transfer_account"],
                    "transfer_amount"    => str_replace(",", "", $_POST["transfer_amount"]),
                    "transfer_note"      => $_POST["transfer_note"],
                    "objective"          => $_POST["objective"]
                );
            } else if ($type == "pc") {
                $data = array(
                    "document_id"   => $document_id,
                    "project"       => $_POST["project"],
                    "customer"      => $_POST["customer"],
                    "bank"          => $_POST["bank"],
                    "account"       => $_POST["account"],
                    "account_name"  => $_POST["account_name"],
                    "note"          => $_POST["note"]
                );
            } else if ($type == "pp") {
                $data = array(
                    "document_id"           => $document_id,
                    "project"               => $_POST["project"],
                    "customer"              => $_POST["customer"],
                    "buy_date"              => $_POST["buy_date"],
                    "payment_max_date"      => $_POST["payment_max_date"],
                    "vendor"                => $_POST["vendor"],
                    "buy_type"              => $_POST["buy_type"],
                    "vendor_bank"           => $_POST["vendor_bank"],
                    "vendor_account"        => $_POST["vendor_account"],
                    "vendor_account_name"   => $_POST["vendor_account_name"]
                );
            }

            $this->do_store($id, $type, $data);
            $this->store_item($type, $data);
        }

        $data = array(
            "document_id"   => $document_id,
            "status"            => $_POST["status"]
        );
        $this->do_store($id, "all", $data);

        return redirect("document/form/$type/$document_id");
    }

    public function store_item($type, $data)
    {
        if ($type != "all") {
            $this->mod_document_item->remove_document_item($data['document_id'] ?? '', $type);
            if ($type == "ca") {
                for ($i = 0; $i < count($_POST['description']); $i++) {
                    if ($_POST['description'][$i] != "") {
                        $data = array(
                            'document_id'     => $data['document_id'],
                            'description'     => $_POST['description'][$i],
                            'unit'            => $_POST['unit'][$i],
                            'price'           => str_replace(",", "", $_POST['price'][$i]),
                            'used'           => str_replace(",", "", $_POST['used'][$i])
                        );

                        move_uploaded_file($_FILES["file"]["tmp_name"][$i], "_shared/upload/" . $_FILES["file"]["name"][$i]);
                        $data['file']  = $_FILES["file"]["name"][$i] != "" ? "_shared/upload/" . $_FILES["file"]["name"][$i] : $_POST['file_'][$i];
                        $this->mod_document_item->add_document_item($type, $data);
                    }
                }
            } else if ($type == "pc") {
                for ($i = 0; $i < count($_POST['description']); $i++) {
                    if ($_POST['description'][$i] != "") {
                        $data = array(
                            'document_id'     => $data['document_id'],
                            'description'     => $_POST['description'][$i],
                            'unit'            => $_POST['unit'][$i],
                            'price'           => str_replace(",", "", $_POST['price'][$i])
                        );

                        move_uploaded_file($_FILES["file"]["tmp_name"][$i], "_shared/upload/" . $_FILES["file"]["name"][$i]);
                        $data['file']  = $_FILES["file"]["name"][$i] != "" ? "_shared/upload/" . $_FILES["file"]["name"][$i] : $_POST['file_'][$i];
                        $this->mod_document_item->add_document_item($type, $data);
                    }
                }
            } else if ($type == "pp") {
                for ($i = 0; $i < count($_POST['item']); $i++) {
                    if ($_POST['item'][$i] != "") {
                        $data = array(
                            'document_id'     => $data['document_id'],
                            'item'            => $_POST['item'][$i],
                            'unit'            => $_POST['unit'][$i],
                            'price'           => str_replace(",", "", $_POST['price'][$i]),
                        );
                        $this->mod_document_item->add_document_item($type, $data);
                    }
                }
            }
        }
    }

    public function do_store($id, $type, $data)
    {
        if ($type != "all") {
            $id = $this->mod_document->get_document($id, $type)[0]["id_2"] ?? "";
        }
        if ($id != "") {
            $this->mod_document->set_document($id, $type, $data);
        } else {
            if ($type == "all") {
                $company_code = $this->mod_company->find_company_by_id($data["company_id"] ?? "")[0]["code"] ?? "GTI";
                $division_code = $this->mod_division->find_division_by_id($data["from_division_id"] ?? "")[0]["code"] ?? "";
                $document_type_code = $this->mod_document_type->find_document_type_by_id($data["document_type_id"] ?? $type)[0]["code"];

                $document_number = $document_type_code . "/" . $division_code . "/" . $company_code . "/" . number_to_roman(date("m")) . "/" . date("Y");
                $document_number = $this->mod_document->find_document_number("%" . $document_number)[0]["last_document_number"] . "/" . $document_number;

                $data["document_number"]  = strtoupper($document_number);
            }
            $id = $this->mod_document->add_document($type, $data);
        }
        if ($type == "all") {
            $data = array(
                "document_id"     => $id,
                "update_date"     => date("Y-m-d H:i:s"),
                "user_update"     => $_SESSION['user']['fullname'],
                "status_update"   => $data["status"],
                "note"            => "OK",
            );
            $this->mod_document_his->add_document_his($data);
        }
        return $id;

        // foreach ($this->mod_user->get_user($_POST["to_division_id"]) as $row) {
        //     $this->send_email($row["email"], $_POST["subject"], $_POST["content"]);
        // }
    }

    public function cetak($type, $id)
    {
        $data = $this->mod_document->get_document($id, $type)[0];

        $template = "_shared/template/" . $data["company_code"] . ".docx";
        $filename = "_shared/surat/";
        $filename .= $data["document_type_name"] . "-" . $data["company_code"] . "-" . str_replace("/", "-", $data["document_number"]);

        $data = array(
            "document_number" => $data["document_number"],
            "subject" => $data["subject"],
            "user_create" => $data["user_create"],
            "from_division" => $data["from_division"],
            "to_division" => $data["to_division"],
            "content" => $data["content"],
            "release_date" => custom_date_format($data["release_date"], 'd F Y')
        );


        $this->edit_word($template, $data, $filename . ".docx");
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
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($template);
        foreach ($data as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }
        $templateProcessor->saveAs($filename);
    }

    function word_to_pdf($filename)
    {
        return redirect($filename . ".docx");
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
}
