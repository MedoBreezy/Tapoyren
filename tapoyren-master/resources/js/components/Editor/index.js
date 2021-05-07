import React, { useEffect } from 'react'

import { Editor } from '@tinymce/tinymce-react';

import config from '../../config'

const tinyMCEApiKey = "dv3w7w1y8cqyl7q3arvbxl4jqeoeewvg0kjj37kg17uupjk6"



export default function TinyEditor(props) {

   const initialValue = ""

   return (
      <Editor
         ref={props.ref ? React.forwardRef(props.ref) : null}
         apiKey={tinyMCEApiKey}
         initialValue={props.initialValue || initialValue}
         value={props.value || ''}
         init={{
            height: props.height || 300,
            menubar: props.menubar !== undefined ? props.menubar : true,
            relative_urls: false,
            convert_urls: false,
            automatic_uploads: true,
            images_upload_url: config.api + '/upload/editor/image',
            images_upload_handler: function (blobInfo, success, failure) {
               var xhr, formData;

               xhr = new XMLHttpRequest();
               xhr.withCredentials = false;
               xhr.open('POST', config.api + '/upload/editor/image');

               xhr.onload = function () {
                  var json;

                  if (xhr.status != 200) {
                     failure('HTTP Error: ' + xhr.status);
                     return;
                  }

                  json = JSON.parse(xhr.responseText);

                  if (!json || typeof json.location != 'string') {
                     failure('Invalid JSON: ' + xhr.responseText);
                     return;
                  }

                  success(json.location);
               };

               formData = new FormData();
               formData.append('file', blobInfo.blob(), blobInfo.filename());

               xhr.send(formData);
            },
            plugins: [
               'advlist autolink lists link image charmap print preview anchor',
               'searchreplace visualblocks code fullscreen',
               'insertdatetime media table paste code help wordcount',
            ],
            toolbar:
               `image | formatselect | bold italic backcolor |
	            alignleft aligncenter alignright alignjustify |
	            bullist numlist outdent indent | removeformat`
         }}
         onEditorChange={props.handleChange || null}
      />
   )


}
