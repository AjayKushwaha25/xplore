!(function (t) {
    "use strict";
    function e() {}
    (e.prototype.init = function () {
        t(".confirmUnmap").click(function () {
        	var outletUUID = $(this).data('outlet-id');
        	var dsID = $(this).data('ds-id');
        	var urlID = url+"/"+outletUUID;
        	console.log(urlID)
        	// return false
            Swal.fire({ title: "Are you sure?", text: "You want to unmap this Outlet", icon: "warning", showCancelButton: !0, confirmButtonColor: "#34c38f", cancelButtonColor: "#f46a6a", confirmButtonText: "Yes, unmap it!" }).then(function (t) {
            	if(t.value){
	            	$.ajax({
				        url: urlID,
				        type: 'POST',
                		dataType: "JSON",
				        data: {
				            // "uuid": outletUUID,
				            "_token": _token,
				        },
				        success: function (response){
				            // console.log("it Works");
                 			Swal.fire("Unmapped!", response.message, "success").then(function (t) {
			                	// console.log(t.value + myText)
			                	location.reload();
			                });
				        }
				    });
	            }
                }
            );
        })	;
    }),
    (t.SweetAlert = new e()),
    (t.SweetAlert.Constructor = e);
})(window.jQuery),
(function () {
	"use strict";
    window.jQuery.SweetAlert.init();
})();
