<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title></title>
    <style>
        .btn-download {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: white;
            color: black;
            border: 1px solid #dadce0;
            border-radius: 4px;
            padding: 8px;
            box-shadow: 0 1px 2px rgba(60, 64, 67, 0.3), 0 1px 3px rgba(60, 64, 67, 0.15);
            cursor: pointer;
            z-index: 1000;
            display: flex;
            align-items: center;
            transition: background-color 0.2s ease;
            text-decoration: none;
        }

        .btn-download:hover {
            background-color: #f1f3f4;
        }

        .btn-download svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>

<body style="margin: 0;">
    <div style="display: none;">
        <input id="files" type="file" accept=".docx" />
        <input type="button" id="btnPreview" value="Preview Word Document" onclick="PreviewWordDoc()" />
    </div>

    <a href="<?= base_url() . $file ?>" download class="btn-download">DOWNLOAD</a>

    <div id="word-container" class=""></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://unpkg.com/jszip/dist/jszip.min.js"></script>
    <script src="<?= base_url() ?>assets/js/docx-preview.js"></script>
    <script type="text/javascript">
        function downloadAsPDF() {
            const element = document.getElementById('word-container');

            const opt = {
                margin: 0.5,
                filename: 'document.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'in',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            html2pdf().set(opt).from(element).save();
        }

        function PreviewWordDoc() {
            var doc = document.getElementById("files").files[0];

            if (doc != null) {
                var docxOptions = Object.assign(docx.defaultOptions, {
                    useMathMLPolyfill: true
                });
                var container = document.querySelector("#word-container");
                docx.renderAsync(doc, container, null, docxOptions);
            }
        }

        $(document).ready(function() {
            async function setInputFileFromURL(inputElement, fileURL, fileName) {
                try {
                    const response = await fetch(fileURL);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const blob = await response.blob();
                    const file = new File([blob], fileName, {
                        type: blob.type
                    });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    inputElement.files = dataTransfer.files;
                    console.log('File set successfully');
                } catch (error) {
                    console.error('Error setting file:', error);
                }
            }

            const inputElement = document.querySelector('#files');
            const fileURL = "<?= base_url() . $file ?>";
            const fileName = 'file.docx';
            setInputFileFromURL(inputElement, fileURL, fileName);

            setTimeout(function() {
                PreviewWordDoc();
            }, 3000);

        });
    </script>
</body>

</html>