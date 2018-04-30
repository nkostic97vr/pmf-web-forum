function initSceditor() {
    sceditor.create(document.querySelector(".sceditor"), {
        width: "100%",
        height: "300px",
        format: "bbcode",
        bbcodeTrim: true,
        spellcheck: false,
        resizeWidth: false,
        toolbarExclude: "email",
        resizeMinHeight: "120px",
        resizeMaxHeight: "1000px",
        locale: $("html").attr("lang"),
        emoticonsRoot: "/lib/sceditor/",
        plugins: "undo",
        toolbar: "bold,italic,underline,strike,subscript,superscript|" +
            "left,center,right,justify|font,size,color,removeformat|" +
            "cut,copy,pastetext|bulletlist,orderedlist,indent,outdent|" +
            "table|bbcodetag,code,quote|horizontalrule,image,email,link,unlink|" +
            "emoticon,youtube,date,time|ltr,rtl|print,maximize,source",
        pastetext: {
            enabled: true,
            addButton: true
        },
        style: "/lib/sceditor/themes/content/default.min.css"
    });
}