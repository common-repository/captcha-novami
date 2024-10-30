let $ = jQuery;

$(document).ready(function () {
    console.log(captcha_novami_js_trans.loaded);
    $('<input>').attr('type','hidden').attr('name','canoto').attr('value', captcha_novami_js_trans.canoto).appendTo('form');
});
