                        <!-- TinyMCE CDN, place before your script -->
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.9.11/tinymce.min.js"></script>

                        <!-- Setup -->
                        <script>
                        tinymce.init({
                            path_absolute: "/",
                            selector: ".tinymce",
                            plugins: [
                                "textcolor advlist autolink lists link image charmap print preview hr anchor pagebreak",
                                "searchreplace wordcount visualblocks visualchars code fullscreen",
                                "insertdatetime media nonbreaking save table directionality",
                                "emoticons template paste textcolor colorpicker textpattern"
                            ],
                            toolbar: "forecolor backcolor insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
                            relative_urls: false,
                            automatic_uploads: true,
                            images_upload_url: '/tinymce-upload', // Laravel upload route
                            images_upload_handler: function (blobInfo, success, failure) {
                                let xhr, formData;
                                xhr = new XMLHttpRequest();
                                xhr.open('POST', '/tinymce-upload');

                                // --- CSRF token for Laravel (grab from meta tag or window.Laravel.csrfToken) ---
                                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                                xhr.onload = function() {
                                    let json;

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
                            }
                        });
                        </script>
