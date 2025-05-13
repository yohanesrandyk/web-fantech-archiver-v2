<style>
    .mini-image-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        align-items: center;
    }

    .mini-image-image-preview {
        margin-top: 10px;
        display: flex;
        flex-wrap: wrap;
    }

    .mini-image-file-input {
        display: none;
    }

    .mini-image-file-label {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 50px;
        border-color: grey;
        background-color: transparent;
        border: 2px solid rgba(128, 128, 128, 0.5);
        opacity: 0.5;
        border-radius: 10%;
        width: 100px;
        height: 100px;
        margin: 5px;
        order: 1;
    }

    .mini-image-file-label:hover {
        color: white;
        background-color: rgba(128, 128, 128, 0.5);
    }

    .mini-image-file-label::before {
        content: "+";
        font-weight: bold;
        color: rgba(128, 128, 128, 0.5);
    }

    .mini-image-file-label:hover::before {
        color: white;
    }

    .mini-image-remove-icon {
        position: absolute;
        top: 5px;
        right: 5px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: transparent;
        color: red;
        text-align: center;
        font-weight: bold;
        cursor: pointer;
    }

    .mini-image-remove-icon::before {
        content: "\f057";
        /* ini adalah kode unicode untuk icon fa-times */
        color: red;
        font-family: "Font Awesome 5 Free";
        /* ini adalah nama font untuk icon Font Awesome */
        font-weight: 900;
        /* ini adalah bobot font untuk icon Font Awesome */
        font-size: 16px;
        /* ini adalah ukuran font untuk icon */
        line-height: 20px;
        /* ini adalah tinggi baris untuk icon */
    }

    .mini-image-image-container {
        position: relative;
        margin: 5px;
        border-radius: 10px;
        order: 0;
    }

    .mini-image-image-container:hover {
        opacity: 0.8;
        transition: opacity 0.3s;
        display: block;
    }

    .mini-image-remove-icon:hover {
        color: red;
        border-color: red;
    }

    .mini-image-image-container img {
        object-fit: cover;
        border-radius: 10px;
        width: 100px;
        height: 100px;
    }

    .typed-cursor {
        font-size: 20px;
        opacity: 1;
        -webkit-animation: blink 0.7s infinite;
        -moz-animation: blink 0.7s infinite;
        animation: blink 0.7s infinite;
    }

    #loading_text {
        font-size: 20px
    }

    #loading {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
    }
</style>
<style>
    .animate__animated {
        animation-duration: 0.5s;
    }
</style>