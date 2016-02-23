function isSet(variable) {
    return typeof variable != "undefined" && variable != null;
}

function getObjectByID(objArray, IDname, ID) {
   for (var i = 0; i < objArray.length; i++) {
      if (objArray[i][IDname] == ID) {
         return objArray[i];
      }
   }
   return null;
}

function getObjectKeyByID(objArray, IDname, ID) {
   for (var i = 0; i < objArray.length; i++) {
      if (objArray[i][IDname] == ID) {
         return i;
      }
   }
   return null;
}

function makeObjectID(objArray, IDname) {
   var newID = 0;
   while (isSet(getObjectByID(objArray, IDname, newID))) {
      newID++;
   }
   return newID;
}