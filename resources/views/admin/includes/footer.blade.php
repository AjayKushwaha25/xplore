<footer class="footer">
     <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                {{ date('Y') }} &copy; Admin Panel.
            </div>
            <div class="col-sm-6">
                 <div class="text-sm-end d-none d-sm-block">
                    Design & Develop by Ottoedge Services LLP
                 </div>
            </div>
        </div>
     </div>
</footer>
<div>

<div class="position-fixed end-0 p-3" style="z-index: 1005;top: 12%">
    <div id="liveToast" class="toast" role="alert" aria-live="polite" aria-atomic="true">
    <div class="toast fade show align-items-center text-white bg-dark border-0"
        role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
            </div>
            {{-- <button type="button" class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast" aria-label="Close"></button> --}}
        </div>
    </div>
</div>
{{-- <div id="liveToast" class="toast" aria-live="polite" aria-atomic="true" class="position-relative">
    <div class="toast-container position-absolute end-0 p-2 p-lg-3" style="z-index: 1005;top: 12%">
        <!-- Then put toasts within -->
        <div class="toast fade show" role="alert" aria-live="assertive" data-bs-autohide="false" aria-atomic="true">
            <div class="toast-header">
                <img src="assets/images/logo.svg" alt="" class="me-2" height="18">
                <strong class="me-auto">Skote</strong>
                <small class="text-muted">just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
            <div class="toast-body">
                See? Just like this.
            </div>
        </div>

        <div class="toast fade show" role="alert" aria-live="assertive" data-bs-autohide="false" aria-atomic="true">
            <div class="toast-header">
                <img src="assets/images/logo.svg" alt="" class="me-2" height="18">
                <strong class="me-auto">Skote</strong>
                <small class="text-muted">2 sec ago</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Heads up, toasts will stack automatically
            </div>
        </div>
    </div>
</div> --}}
<!-- JAVASCRIPT -->
<script src="{{ asset('admin/js/jquery.min.js') }}"></script>
<script src="{{ asset('admin/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('admin/js/simplebar.min.js') }}"></script>
<script src="{{ asset('admin/js/waves.min.js') }}"></script>
<script src="{{ asset('admin/js/bootstrap-toastr.init.js') }}"></script>

<!-- App js -->
<script src="{{ asset('admin/js/app.js') }}"></script>
<script type="text/javascript">
    function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(element.dataset.url).select();
        document.execCommand("copy");

        // 
        toast = $("#liveToast");
        toastMsg = $("#liveToast .toast-body")
        toastMsg.html(element.dataset.msg);
        new bootstrap.Toast(toast).show()

        // 
        $temp.remove();
    }
    function emailValid(email, emailErr){
        atpos = email.indexOf("@");
        dotpos = email.lastIndexOf(".");
        if(atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length){
            emailErr.show();
            return false;
        }
        emailErr.hide();
        return true;
    }
    //onkeypress attribute to be added with return keyword
    function NumberOnly(e){
        var k;
        document.all ? k = e.keyCode : k = e.which;
        return ((k > 47 && k < 58) || k == 8 || k == 0);
    }

    function contactValid(contact, contactErr){
        if(contact.length<10){
            contactErr.show();
            return false;                
        }
        contactErr.hide();
        return true;
    }

    function inputValid(inputs, inputsID, inputsErr){
        userInput = inputs;
        showError = inputsErr;
        if (userInput=='' || userInput==null || userInput=='-1' || userInput.length<=0) {
            showError.show();
            inputsID.focus();
            inputsID.css('border-color','#dc3545');
            return false;
        }
        showError.hide();
        inputsID.css('border-color','#ced4da');
        return true;
    }
</script>
