var ApiUri = "API";

$.ajax({
    url: ApiUri,
    data: { action: "config" },
    type: "GET",
    dataType: "json",
    success: function(config) {
        console.log(config);
        
        $("#bundles").loadTemplate($("#tpl-bundle"), config.inputs);
        tplLoaded();
    }
});

function tplLoaded() {
    $(".minify-btn").click(function() {
        var inputID = $(this).attr("value");
        var bundle = $(this).closest(".bundle");
        
        startLoading();
        
        $.ajax({
            url: ApiUri,
            data: { action: "minify", inputID: inputID},
            type: "GET",
            dataType: "json",
            success: function(data) {
                console.log(data);
                bundle.find(".bundle-error").addClass("hide");
            },
            error: function(data) {
                console.log(data);
                var details = "";
                
                if (isSet(data.responseJSON)) {
                    if (data.responseJSON.CSS > 200) {
                        details += "There was a problem progressing your CSS files. ";
                    }
                    if (data.responseJSON.JS > 200) {
                        details += "There was a problem progressing your JS files. ";
                    }
                }
                
                bundle.find(".bundle-error").removeClass("hide");
                bundle.find(".bundle-error-details").empty();
                bundle.find(".bundle-error-details").text(details);
            },
            complete: function() {
                finishLoading();
            }
        });
    });
}

function startLoading() {
    $("#loading, #loading-wrapper").fadeIn();
}

function finishLoading() {
    $("#loading, #loading-wrapper").fadeOut();
}