const openPageObj = require('/module/openPage.js');

const formatTime = date => {
  const year = date.getFullYear()
  const month = date.getMonth() + 1
  const day = date.getDate()
  const hour = date.getHours()
  const minute = date.getMinutes()
  const second = date.getSeconds()

  return [year, month, day].map(formatNumber).join('/') + ' ' + [hour, minute, second].map(formatNumber).join(':')
}

const formatNumber = n => {
  n = n.toString()
  return n[1] ? n : '0' + n
}

function DeepCopy(object) {
  let resultObject = {};
  for (let obj in object) {
      if (typeof (object[obj]) == "object" && !Array.isArray(object[obj])) {
          let x = {}
          x[obj] = DeepCopy(object[obj])
          Object.assign(resultObject, x);
      } else {
          let x = {};
          x[obj] = object[obj];
          Object.assign(resultObject, x);
      }
  }
  return resultObject;
}

module.exports = {
  formatTime: formatTime,
  openPage: openPageObj.openPage,
  thisPageData: openPageObj.thisPageData,
  urlEncode: openPageObj.urlEncode,
  DeepCopy
}
