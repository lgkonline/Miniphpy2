var ApiUri = "API";
var Config = {};

$.ajax({
    url: ApiUri,
    data: { action: "config" },
    type: "GET",
    dataType: "json",
    success: function(data) {
        Config = data;
        console.log(Config);
        
        $("#bundles").loadTemplate($("#tpl-bundle"), Config.inputs);
        tplLoaded();
    }
});

function saveConfig() {
    console.log(JSON.stringify(Config));
    
    $.ajax({
        url: ApiUri,
        data: { action: "set-config", config: JSON.stringify(Config) },
        type: "POST",
        dataType: "json",
        success: function(data) {
            console.log(data);
        },
        error: function(data) {
            console.log(data);
        }
    });
}

function tplLoaded() {
    $(".minify-btn").click(function() {
        var bundle = $(this).closest(".bundle");
        var inputID = bundle.attr("value");
        
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
    
    $(".minify-input").change(function() {
        var inputID = $(this).closest(".bundle").attr("value");
        var inputKey = getObjectKeyByID(Config.inputs, "inputID", inputID);
        Config.inputs[inputKey].inputFile = $(this).val();
        
        console.log(Config.inputs[inputKey]);
        saveConfig();
    });
}

function startLoading() {
    $("#loading, #loading-wrapper").fadeIn();
}

function finishLoading() {
    $("#loading, #loading-wrapper").fadeOut();
}