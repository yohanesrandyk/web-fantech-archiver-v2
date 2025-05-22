<?php
defined("BASEPATH") or exit("No direct script access allowed");

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
                "user_note"         => $_POST["user_note"] ?? '',
                "release_date"      => $_POST["release_date"] ?? '',
                "create_date"       => $_POST["create_date"] ?? '',
                "status"            => $_POST["status"] ?? '',
            );
            $document_id = $this->do_store($id, "all", $data);
            if ($type == "ca" || $type == "pc" || $type == "pp" || $type == "pr") {
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
                } else if ($type == "pp" || $type == "pr") {
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
        // $this->db->trans_rollback();
        $this->db->trans_commit();

        $_SESSION['old'] = null;

        return redirect("document/form/$type/$document_id");
    }

    public function store_file($document_id)
    {
        $isfile = false;
        $this->mod_file_document->remove_file_document($document_id ?? '');
        for ($i = 0; $i < count($_POST['note_f'] ?? []); $i++) {
            if (!empty($_POST['note_f'][$i] ?? '') || !empty($_FILES["file_f"]["tmp_name"][$i])) {
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
        if ($type == "ca" || $type == "pc" || $type == "pp" || $type == "pr") {
            $this->mod_document_item->remove_document_item($data['document_id'] ?? '', $type);
            for ($i = 0; $i < count($_POST['description'] ?? []); $i++) {
                if (!empty($_POST['description'][$i] ?? '') || !empty($_FILES["file"]["tmp_name"][$i])) {
                    $data = array(
                        'document_id'     => $data['document_id'],
                        'description'     => $_POST['description'][$i] ?? '',
                        'description_1'   => $_POST['description_1'][$i] ?? '',
                        'description_2'   => $_POST['description_2'][$i] ?? '',
                        'unit'            => $_POST['unit'][$i] ?? '0',
                        'price'           => str_replace(",", "", $_POST['price'][$i] ?? '0')
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
                "user_update"      => $_POST["user_note"],
                "status_update"    => $_POST["status"],
                "note"             => $_POST["note"],
                "from_division_id" => $_SESSION['user']['division_id'],
                "user_update_id"   => $_SESSION['user']['id'],
            );
            $this->mod_document_his->add_document_his($data);

            $division_id = $this->mod_docstatus->get_docstatus($_POST["status"]);
            $division_id = !empty($division_id) ? $division_id[0]['to_division_id'] : "0";
            $division_id = $division_id != '0' ? $division_id : $_SESSION['user']['division_id'];
            $document_number_ = $_POST["document_number"] ?? "";
            $document_number_ = !empty($document_number_) ? $document_number_ : $document_number;
            if (!empty($document_number_)) {
                $notification = array(
                    "title"     => 'PEMBERITAHUAN! TERDAPAT DOKUMEN DENGAN NOMOR ' . $document_number_,
                    "body"  =>  $_SESSION['user']['username'] . " : HARAP DAPAT SEGERA MELAKUKAN REVIEW TERHADAP DOKUMEN. " . $_POST["note"],
                    "to_division_id" => $division_id,
                    "notif_date" => date("Y-m-d H:i:s"),
                );
                $this->mod_notification->add_notification($notification);
                foreach ($this->mod_user->get_user('%', $division_id) as $row) {
                    send_notif($row["fcm_token"], $notification['title'], $notification['body']);
                }
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
        $filename .= str_replace('/', '-', $data["doctype_name"]) . "-" . $data["company_code"] . "-" . str_replace("/", "-", $data["document_number"]);

        $docstatus = $this->mod_docstatus->get_docstatus('%', '%', '%' . $type . '%', '%');
        foreach ($docstatus as $row) {
            if ($row["to_division"] != 'SEMUA DIVISI') {
                $docstatus = $row["to_division"];
            }
        }

        $data_ = array(
            "document_number" => $data["document_number"],
            "subject" => $data["doctype_name"],
            "user_create" => strtoupper($data["user_create"]),
            "from_division" => $data["from_division"],
            "to_division" => $docstatus,
            "html_content" => $data["content"],
            "release_date" => custom_date_format($data["release_date"], 'd F Y')
        );
        $data_["release_date"] = ucwords(strtolower($data_["release_date"]));

        if ($type == "ca" || $type == "pc" || $type == "pp" || $type == "pr") {
            $data_his_pd = $this->mod_document_his->get_document_his($id, 'PAC');
            $data_his_ap = $this->mod_document_his->get_document_his($id, 'AAC');
            $data_his_ac = $this->mod_document_his->get_document_his($id, 'ACD');
            $data_his_dr = $this->mod_document_his->get_document_his($id, 'ADF');
            $data_his_fn = $this->mod_document_his->get_document_his($id, 'AFA');

            $data_["document_number"] = $data["document_number"];
            $data_["user_create"] = $data["user_create"];
            $data_["from_division"] = $data["from_division"];
            $data_["create_date"] = custom_date_format($data["create_date"], 'd F Y');
            $data_["create_date"] = ucwords(strtolower($data_["create_date"]));

            $data_["user_pend"] = ($data_his_pd[0]['user_update'] ?? '');
            $data_["pend_date"] = ((!empty($data_his_pd) ? custom_date_format($data_his_pd[0]['update_date'], 'd/m/Y H:i:s') : ''));
            $data_["user_appr"] = ($data_his_ap[0]['user_update'] ?? '');
            $data_["appr_date"] = ((!empty($data_his_ap) ? custom_date_format($data_his_ap[0]['update_date'], 'd/m/Y H:i:s') : ''));
            $data_["user_acc"] =  ($data_his_ac[0]['user_update'] ?? '');
            $data_["acc_date"] =  ((!empty($data_his_ac) ? custom_date_format($data_his_ac[0]['update_date'], 'd/m/Y H:i:s') : ''));
            $data_["user_dir"] =  ($data_his_dr[0]['user_update'] ?? '');
            $data_["dir_date"] =  ((!empty($data_his_dr) ? custom_date_format($data_his_dr[0]['update_date'], 'd/m/Y H:i:s') : ''));
            $data_["user_fin"] =  ($data_his_fn[0]['user_update'] ?? '');
            $data_["fin_date"] =  ((!empty($data_his_fn) ? custom_date_format($data_his_fn[0]['update_date'], 'd/m/Y H:i:s') : ''));

            $data_['tujuan'] = $data["project"];

            if ($type == "ca" || $type == "pc" || $type == "pr") {
                $data_["table_item"] = array();
                $data_["image_item"] = array();
                if ($type == "pr") {
                    $data_["table_item"][] = array("No", "Keterangan", "Spesifikasi / Ukuran", "Quantity", "Leadtime", "Jumlah");
                } else {
                    $data_["table_item"][] = array("No", "Keterangan", "Jumlah");
                }
                $no = 1;
                $total = 0;
                foreach ($this->mod_document_item->get_document_item($id, $type) as $row) {
                    if ($row["price"] == '0') continue;
                    $subtotal = (int) $row["unit"] * (int) $row["price"];
                    $total += $subtotal;
                    if ($type == "pr") {
                        array_push($data_["table_item"], array(
                            $no++,
                            $row["description"] . " Rp. " . number_format($row["price"]),
                            $row["description_1"],
                            $row["unit"],
                            $row["description_2"],
                            number_format($subtotal)
                        ));
                    } else {
                        array_push($data_["table_item"], array(
                            $no++,
                            $row["description"] . " " . $row["unit"] . "x @" . number_format($row["price"]),
                            number_format($subtotal)
                        ));
                    }

                    array_push($data_["image_item"], $row["file"] ?? null);
                }
                if ($type == "pr") {
                    array_push($data_["table_item"], array("", "", "", "",  "Total", number_format($total)));
                } else {
                    array_push($data_["table_item"], array("", "Total", number_format($total)));
                }
                if ($type == "ca") {
                    array_push($data_["table_item"], array("", "Jumlah yang diterima", number_format($data['transfer_amount'])));
                }
                $data_["transfer_date"] = !str_contains($data["transfer_date"], '0000') ? custom_date_format($data["transfer_date"] ?? '', 'd F Y') : '';
                $data_["transfer_date"] = ucwords(strtolower($data_["transfer_date"]));
            }

            if ($type == 'pp') {
                $data_['transfer_amount'] = number_format($data['transfer_amount']);
                $data_['transfer_amount_text'] = terbilang($data['transfer_amount']) . "rupiah";
                $data_['transfer_method'] = ($data["transfer_method"] ?? '') == 'B' ? 'TRANSFER' : 'TUNAI';
                $data_['transfer_account'] = $data["transfer_account"] ?? '';
                $data_['transfer_bank'] = $data["transfer_bank"] ?? '';
                $data_['transfer_account_name'] = ' atas nama ' . ($data["transfer_account_name"] ?? '');
                $data_["transfer_date"] = !str_contains($data["transfer_date"], '0000') ? custom_date_format($data["transfer_date"] ?? '', 'd F Y') : '';
                $data_["transfer_date"] = ucwords(strtolower($data_["transfer_date"]));
            }
        }

        $data_["print_date"] = "TANGGAL CETAK : " . custom_date_format(date("Y-m-d H:i:s"), 'd F Y H:i:s') . ' . ' . $data["user_create"] . ' . CETAKAN KE-' . (($data["print"] ?? 0) + 1);
        $data_["print_date"] = strtoupper($data_["print_date"]);

        edit_word($template, $data_, $filename);
        docx_to_pdf_api($filename);
        show_pdf($filename);
    }

    public function delete($type, $id)
    {
        if ($type != "all") {
            $id = $this->mod_document->get_document($id, $type)[0]["id_2"] ?? "";
        }
        $this->mod_document->remove_document($id, $type);
        return redirect("document/index/$type");
    }

    function show_word()
    {
        $data["file"] = $_GET["file"];
        $this->load->view("cetak", $data);
    }
}
