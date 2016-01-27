function isSet(variable) {
    return typeof variable != "undefined" && variable != null;
}

function getObjectFromArray(objArray, IDname, ID) {
    for (var i = 0; i < objArray.length; i++) {
        if (objArray[IDname] == ID) {
            return objArray[IDname];
        }
    }
    return null;
}

function makeID(objArray, IDname) {
    var newID = 1;
    
    while (isSet(getObjectFromArray(objArray, IDname, newID))) {
        newID++;
    }
    
    return newID;
}