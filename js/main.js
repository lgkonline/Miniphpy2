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

function saveConfig(reload) {
    if (!isSet(reload)) {
        reload = false;
    }
    
    var sendingConfig = JSON.stringify(Config);
    
    $.ajax({
        url: ApiUri + "?action=set-config",
        type: "GET",
        dataType: "json",
        data: { 
            "action": "set-config", 
            "receivedConfig": sendingConfig
        },
        success: function(data) {
            console.log(data);
            
            if (reload) {
                location.reload();
            }
        },
        error: function(data) {
            console.error(data);
            alert("There was an error on saving the config.");
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
    
    $(".remove-input").click(function() {
        var inputID = $(this).attr("value");
        var inputKey = getObjectKeyByID(Config.inputs, "inputID", inputID);
        Config.inputs.splice(inputKey, 1);
        saveConfig(true);
    });
    
    $(".bundle-title").click(function() {
        toggleBundleTitle($(this).closest(".bundle"));
        $(this).closest(".bundle").find(".bundle-title-input").select();
    });
    
    $(".bundle-title-input").blur(function() {
        toggleBundleTitle($(this).closest(".bundle"));
    });
    
    $(".bundle-title-input").change(function() {
        $(this).closest(".bundle").find(".bundle-title").text($(this).val());
        
        var inputID = $(this).closest(".bundle").attr("value");
        var inputKey = getObjectKeyByID(Config.inputs, "inputID", inputID);
        Config.inputs[inputKey].title = $(this).val();
        
        console.log(Config.inputs[inputKey]);
        saveConfig();
    });
}

function toggleBundleTitle(bundleElement) {
    $(bundleElement).find(".bundle-title").toggle();
    $(bundleElement).find(".bundle-title-input").toggleClass("hide");
}

function addInput() {
    Config.inputs.push({
        "inputID": makeObjectID(Config.inputs, "inputID"),
        "inputFile": "",
        "title": "No title"
    });
    saveConfig(true);
}

function startLoading() {
    $("#loading, #loading-wrapper").fadeIn();
}

function finishLoading() {
    $("#loading, #loading-wrapper").fadeOut();
}

$(document).ready(function() {
    $(".lgk-logo").tooltip({
        placement: "left"
    });
});