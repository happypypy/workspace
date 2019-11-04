layui.define(['upload'],function (exports) {
    var upload = layui.upload;
    function uploadImg (elementId, inputId, saveDirectory) {
        upload.render({
            elem: '#' + elementId
            , url: "/admin/Uploadify/imageUpload"
            , accept: 'images'
            , acceptMime: 'image/*'
            , field: 'image_'+inputId
            , multiple: true
            , data: { 'field': inputId, saveDirectory: saveDirectory}
            , done: function (res) {
                if (res.code == -1) {
                    return layer.msg('上传失败');
                }
                $('#' + inputId).val(res.data.filepath);
                $('#' + inputId +'_preview').attr('src', res.data.filepath);
            }
        });
    }
    exports('uploadImg', uploadImg);
});