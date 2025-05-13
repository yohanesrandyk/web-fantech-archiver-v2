<script>
    function initSelectPicker(e) {
        var target = $(e).closest("div").find("select");
        target.selectpicker("refresh");
        target = $(e).closest("div").find("div").find("button");
        $(target[1]).remove();
    }

    function setCurrency(e) {
        $(document).ready(function() {
            var nilai = $(e).val();
            if (nilai.indexOf(".") > 0) nilai = nilai.substr(0, nilai.indexOf("."));
            nilai = nilai.replace(/,/g, '');
            nilai = formatNumber(nilai);
            if (nilai.indexOf(".") > 0) nilai = nilai.substr(0, nilai.indexOf("."));
            if (nilai == "NaN") nilai = 0;
            $(e).val(nilai);
        });
    }
</script>
<script>
    function updateLabel(input) {
        var fileName = input.files[0].name;
        input.nextElementSibling.innerHTML = fileName;
    }

    function add_tab_item(index, item_container, content_container, content) {
        $("#" + item_container).append("<div class='card card-custom mb-3 animate__animated animate__fadeIn' target='" + item_container + "-" + index + "'>" + $("#tab-custom-item").html() + "</div>");
        $("#" + content_container).append("<div class='card-body tab-custom d-none animate__animated animate__fadeIn' id='" + item_container + "-" + index + "'>" + $("#" + content).html() + "</div>");
    }

    function open_form(e) {
        var target = $(e).closest('.card-custom').attr('target');
        console.log(target);
        $(".tab-custom").each(function(i, e) {
            $(e).addClass("d-none animate__animated animate__fadeOut").removeClass("animate__fadeIn");
        });
        $("#" + target).removeClass('d-none').addClass("animate__animated animate__fadeIn").removeClass("animate__fadeOut");
    }

    function delete_item(e) {
        var target = $(e).closest('.card-custom').attr('target');
        $('#' + target).remove();
        $(e).closest('.card-custom').addClass("animate__animated animate__fadeOut").one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e) {
            $(this).remove();
        });
    }

    function show_loading() {
        $("#loading").show();
    }
    $("a").click(function() {
        if (this.href != null)
            if (this.href.indexOf("#") == -1 && this.target.indexOf("_blank") == -1) show_loading();
    });
    $("form").submit(function() {
        show_loading();
    });

    function escSpace(text) {
        if (text != null) return text.replace(/ /g, '_');
        return "-";
    }

    function descSpace(text) {
        return text.replace(/_/g, " ");
    }

    $(".m-item").click(function() {
        $("#kt_aside_mobile_toggle").click();
    })

    function replace_url(key, value) {
        show_loading();
        var url = window.location.href;
        var indexKey = url.indexOf(key);
        var newUrl = "";
        if (indexKey > 0) {
            var url2 = url.substr(indexKey, url.length);
            var indexValue = url2.indexOf('&');
            if (indexValue < 1) indexValue = url2.length;
            url2 = url2.substring(indexValue);
            url = url.substring(0, indexKey);
            newUrl = url + key + "=" + value + url2;
        } else {
            newUrl = url + "&" + key + "=" + value;
        }
        window.location.replace(newUrl);
    }

    function remove_url(url, key) {
        show_loading();
        var indexKey = url.indexOf(key);
        var newUrl = "";
        if (indexKey > 0) {
            var url2 = url.substr(indexKey, url.length);
            var indexValue = url2.indexOf('&');
            if (indexValue < 1) indexValue = url2.length;
            url2 = url2.substring(indexValue);
            url = url.substring(0, indexKey);
            newUrl = url + url2;
        } else {
            newUrl = url;
        }
        window.location.replace(newUrl);
    }
    $(document).ready(function() {
        $("#datatable").DataTable();
        $("#datatable2").DataTable();
    });

    function toggleAccordion(e) {
        var target = $(e).closest(".card").find(".collapse");
        console.log(target);
        target.collapse("toggle");
    }
</script>
<script>
    function getSelectValue(e) {
        var value = $(e).find("option:selected").text();
        value = value.substring(value.indexOf("[") + 1, value.indexOf("]"));
        return value;
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typed.js/2.0.0/typed.min.js"></script>
<script>
    const monthNames = ["", "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
    };

    function showImage(e, caption) {
        var image = $(e).css("background-image").replace('url(', '').replace(')', '').replace(/\"/gi, "");
        $("#modal-image-content").attr("src", image);
        $("#modal-image").modal('show');
        $(".modal-caption").text(caption);
    }

    function formatNumberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function formatNumber(number) {
        var nilai = (parseInt(number)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        if (nilai.indexOf(".") > 0) nilai = nilai.substr(0, nilai.indexOf("."));
        return nilai;
    }

    function showMessage(title, message) {
        $("#message-title").text(title);
        $("#message-text").text(message);
        $("#modal-message").modal('show');
    }
</script>