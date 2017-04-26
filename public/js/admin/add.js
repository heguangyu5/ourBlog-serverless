/* global editormd */
$(document).ready(function () {
    editormd('editormd', {
        path: '/editormd/lib/',
        height: 600,
        toolbarIcons: [
            'undo', 'redo', '|',
            'bold', 'del', 'italic', 'quote', 'h2', 'h3', '|',
            'list-ul', 'list-ol', 'hr', '|',
            'link',  'image', 'code', 'preformatted-text', 'code-block', 'table', 'html-entities', '|',
            'preview', 'fullscreen', 'clear', 'search', 'help', 'info'
        ],
        placeholder: '正文',
        imageUpload: true,
        imageFormats: ['jpg', 'png', 'gif', 'zip', 'tar.gz', 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'md', 'txt', 'c', 'php'],
        imageUploadURL: '/admin/upload'
    });
    $('#externalPost').click(function () {
        if ($(this).prop('checked')) {
            $('#editormd').addClass('hide').find('> textarea').removeAttr('name');
            $('#externalPostUrl').attr('name', 'content').parent().removeClass('hide');
        } else {
            $('#externalPostUrl').removeAttr('name').parent().addClass('hide');
            $('#editormd > textarea').attr('name', 'content').parent().removeClass('hide');
        }
    });
});
