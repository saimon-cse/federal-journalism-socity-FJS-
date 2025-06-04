<!-- TinyMCE Self-hosted 7.9.0 with Enhanced Fonts and Image Editing -->
<script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
<script>
    tinymce.init({
        selector: ".tinymce",

        // Core plugins - updated for v7 with image editing plugins
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount',
            'emoticons', 'template', 'paste', 'directionality', 'pagebreak',
            'nonbreaking', 'save', 'imagetools', 'editimage'
        ],

        // Enhanced toolbar with image editing options
        toolbar: 'undo redo | styles fontfamily fontsize | ' +
                'bold italic underline strikethrough | forecolor backcolor | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | ' +
                'link image media table | ' +
                'imagetools rotateleft rotateright flipv fliph | ' +
                'emoticons charmap removeformat | code help',

        // Menu bar with image editing menu
        menubar: 'file edit view insert format tools table image help',

        // Enhanced font families with web-safe and Google Fonts
        font_family_formats:
            'Arial=arial,helvetica,sans-serif;' +
            'Arial Black=arial black,sans-serif;' +
            'Comic Sans MS=comic sans ms,sans-serif;' +
            'Courier New=courier new,courier,monospace;' +
            'Georgia=georgia,serif;' +
            'Helvetica=helvetica,arial,sans-serif;' +
            'Impact=impact,sans-serif;' +
            'Lucida Console=lucida console,monaco,monospace;' +
            'Lucida Sans Unicode=lucida sans unicode,lucida grande,sans-serif;' +
            'Palatino Linotype=palatino linotype,book antiqua,palatino,serif;' +
            'Tahoma=tahoma,geneva,sans-serif;' +
            'Times New Roman=times new roman,times,serif;' +
            'Trebuchet MS=trebuchet ms,geneva,sans-serif;' +
            'Verdana=verdana,geneva,sans-serif;' +
            'Symbol=symbol;' +
            'Webdings=webdings;' +
            'Wingdings=wingdings,zapf dingbats;' +
            'MS Sans Serif=ms sans serif,sans-serif;' +
            'MS Serif=ms serif,serif;' +
            // Google Fonts (you need to include them in your HTML)
            'Open Sans=Open Sans,sans-serif;' +
            'Roboto=Roboto,sans-serif;' +
            'Poppins=Poppins,sans-serif;' +
            'Montserrat=Montserrat,sans-serif;' +
            'Lato=Lato,sans-serif;' +
            'Nunito=Nunito,sans-serif;' +
            'Source Sans Pro=Source Sans Pro,sans-serif;' +
            'Raleway=Raleway,sans-serif;' +
            'Ubuntu=Ubuntu,sans-serif;' +
            'Playfair Display=Playfair Display,serif;' +
            'Merriweather=Merriweather,serif;' +
            'Libre Baskerville=Libre Baskerville,serif;' +
            'Crimson Text=Crimson Text,serif;' +
            'Fira Code=Fira Code,monospace;' +
            'Source Code Pro=Source Code Pro,monospace;' +
            'JetBrains Mono=JetBrains Mono,monospace',

        // Enhanced font sizes
        font_size_formats: '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 28pt 32pt 36pt 48pt 60pt 72pt',

        // Image editing configuration
        imagetools_cors_hosts: ['mydomain.com', 'otherdomain.com'],
        imagetools_proxy: '/tinymce-imageproxy',

        // Image toolbar with editing options
        image_toolbar: 'alignleft aligncenter alignright | rotateleft rotateright | flipv fliph | editimage imageoptions',

        // Advanced image options
        image_advtab: true,
        image_class_list: [
            {title: 'None', value: ''},
            {title: 'Responsive', value: 'img-responsive'},
            {title: 'Rounded', value: 'img-rounded'},
            {title: 'Circle', value: 'img-circle'},
            {title: 'Thumbnail', value: 'img-thumbnail'},
            {title: 'Left Float', value: 'img-left'},
            {title: 'Right Float', value: 'img-right'},
            {title: 'Center', value: 'img-center'}
        ],

        // Image caption options
        image_caption: true,
        image_list: [
            {title: 'Dog', value: 'mydog.jpg'},
            {title: 'Cat', value: 'mycat.gif'}
        ],

        // Image dimensions and constraints
        image_dimensions: true,
        image_uploadtab: true,

        // Custom image styles
        image_description: true,
        image_title: true,

        // Content CSS to include Google Fonts and custom styles
        content_css: [
            '//fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700&family=Lato:wght@300;400;700&family=Nunito:wght@300;400;600;700&family=Source+Sans+Pro:wght@300;400;600;700&family=Raleway:wght@300;400;500;600;700&family=Ubuntu:wght@300;400;500;700&family=Playfair+Display:wght@400;700&family=Merriweather:wght@300;400;700&family=Libre+Baskerville:wght@400;700&family=Crimson+Text:wght@400;600&family=Fira+Code:wght@300;400;500&family=Source+Code+Pro:wght@300;400;500;600&family=JetBrains+Mono:wght@300;400;500;600&display=swap',
            'data:text/css;charset=UTF-8,' + encodeURIComponent(`
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                    font-size: 14px;
                    line-height: 1.6;
                    color: #333;
                }
                .mce-content-body {
                    max-width: none;
                    margin: 0;
                    padding: 20px;
                }
                h1, h2, h3, h4, h5, h6 {
                    margin-top: 1em;
                    margin-bottom: 0.5em;
                    font-weight: 600;
                }
                p {
                    margin: 0 0 1em 0;
                }
                blockquote {
                    border-left: 4px solid #ddd;
                    margin: 1em 0;
                    padding: 0.5em 1em;
                    font-style: italic;
                    background: #f9f9f9;
                }
                code {
                    background: #f4f4f4;
                    padding: 2px 4px;
                    border-radius: 3px;
                    font-family: 'Fira Code', 'Source Code Pro', 'JetBrains Mono', monospace;
                }
                pre {
                    background: #f4f4f4;
                    padding: 1em;
                    border-radius: 5px;
                    overflow-x: auto;
                }
                table {
                    border-collapse: collapse;
                    width: 100%;
                    margin: 1em 0;
                }
                table td, table th {
                    border: 1px solid #ddd;
                    padding: 8px;
                }
                table th {
                    background-color: #f2f2f2;
                    font-weight: 600;
                }

                /* Image styling classes */
                .img-responsive {
                    max-width: 100%;
                    height: auto;
                }
                .img-rounded {
                    border-radius: 6px;
                }
                .img-circle {
                    border-radius: 50%;
                }
                .img-thumbnail {
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    padding: 4px;
                    background-color: #fff;
                }
                .img-left {
                    float: left;
                    margin: 0 15px 15px 0;
                }
                .img-right {
                    float: right;
                    margin: 0 0 15px 15px;
                }
                .img-center {
                    display: block;
                    margin: 0 auto;
                }

                /* Image caption styling */
                figure.image {
                    display: table;
                    margin: 1em auto;
                }
                figure.image img {
                    display: block;
                }
                figure.image figcaption {
                    display: table-caption;
                    caption-side: bottom;
                    background: #f5f5f5;
                    padding: 10px;
                    font-style: italic;
                    text-align: center;
                    border: 1px solid #ddd;
                    border-top: none;
                }

                /* Image hover effects */
                img {
                    transition: all 0.3s ease;
                }
                img:hover {
                    opacity: 0.9;
                    transform: scale(1.02);
                }
            `)
        ],

        // Style formats for quick formatting (including image styles)
        style_formats: [
            {
                title: 'Headings', items: [
                    {title: 'Heading 1', format: 'h1'},
                    {title: 'Heading 2', format: 'h2'},
                    {title: 'Heading 3', format: 'h3'},
                    {title: 'Heading 4', format: 'h4'},
                    {title: 'Heading 5', format: 'h5'},
                    {title: 'Heading 6', format: 'h6'}
                ]
            },
            {
                title: 'Inline', items: [
                    {title: 'Bold', format: 'bold'},
                    {title: 'Italic', format: 'italic'},
                    {title: 'Underline', format: 'underline'},
                    {title: 'Strikethrough', format: 'strikethrough'},
                    {title: 'Superscript', format: 'superscript'},
                    {title: 'Subscript', format: 'subscript'},
                    {title: 'Code', format: 'code'}
                ]
            },
            {
                title: 'Blocks', items: [
                    {title: 'Paragraph', format: 'p'},
                    {title: 'Blockquote', format: 'blockquote'},
                    {title: 'Div', format: 'div'},
                    {title: 'Pre', format: 'pre'}
                ]
            },
            {
                title: 'Image Styles', items: [
                    {
                        title: 'Responsive Image',
                        selector: 'img',
                        classes: 'img-responsive'
                    },
                    {
                        title: 'Rounded Image',
                        selector: 'img',
                        classes: 'img-rounded'
                    },
                    {
                        title: 'Circle Image',
                        selector: 'img',
                        classes: 'img-circle'
                    },
                    {
                        title: 'Thumbnail Image',
                        selector: 'img',
                        classes: 'img-thumbnail'
                    },
                    {
                        title: 'Float Left',
                        selector: 'img',
                        classes: 'img-left'
                    },
                    {
                        title: 'Float Right',
                        selector: 'img',
                        classes: 'img-right'
                    },
                    {
                        title: 'Center Image',
                        selector: 'img',
                        classes: 'img-center'
                    }
                ]
            },
            {
                title: 'Custom Styles', items: [
                    {
                        title: 'Highlight Box',
                        block: 'div',
                        classes: 'highlight-box',
                        styles: {
                            'background-color': '#fff3cd',
                            'border': '1px solid #ffeaa7',
                            'border-radius': '4px',
                            'padding': '10px',
                            'margin': '10px 0'
                        }
                    },
                    {
                        title: 'Call to Action',
                        inline: 'span',
                        classes: 'cta-text',
                        styles: {
                            'background-color': '#007bff',
                            'color': 'white',
                            'font-weight': 'bold',
                            'padding': '4px 8px',
                            'border-radius': '4px'
                        }
                    },
                    {
                        title: 'Large Text',
                        inline: 'span',
                        styles: {
                            'font-size': '1.2em',
                            'font-weight': '500'
                        }
                    },
                    {
                        title: 'Small Text',
                        inline: 'span',
                        styles: {
                            'font-size': '0.8em',
                            'color': '#666'
                        }
                    }
                ]
            }
        ],

        // Image upload configuration
        automatic_uploads: true,
        images_upload_url: '/tinymce-upload',
        images_upload_credentials: true,

        // Updated image upload handler
        images_upload_handler: function (blobInfo, progress) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/tinymce-upload');

                // CSRF token for Laravel - multiple fallback methods
                let csrfToken = '';

                // Method 1: Try meta tag
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    csrfToken = metaTag.getAttribute('content');
                }
                // Method 2: Try Laravel global (if available)
                else if (typeof window.Laravel !== 'undefined' && window.Laravel.csrfToken) {
                    csrfToken = window.Laravel.csrfToken;
                }
                // Method 3: Use the blade token directly (your original method)
                else {
                    csrfToken = '{{ csrf_token() }}';
                }

                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

                xhr.upload.onprogress = (e) => {
                    progress(e.loaded / e.total * 100);
                };

                xhr.onload = () => {
                    if (xhr.status === 403) {
                        reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                        return;
                    }

                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('HTTP Error: ' + xhr.status);
                        return;
                    }

                    const json = JSON.parse(xhr.responseText);

                    if (!json || typeof json.location != 'string') {
                        reject('Invalid JSON: ' + xhr.responseText);
                        return;
                    }

                    resolve(json.location);
                };

                xhr.onerror = () => {
                    reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                };

                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                xhr.send(formData);
            });
        },

        // File picker for additional file types including images
        file_picker_callback: function(callback, value, meta) {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');

            if (meta.filetype === 'image') {
                input.setAttribute('accept', 'image/*');
            } else if (meta.filetype === 'media') {
                input.setAttribute('accept', 'video/*,audio/*');
            } else {
                input.setAttribute('accept', '*/*');
            }

            input.onchange = function() {
                const file = this.files[0];
                const reader = new FileReader();

                reader.onload = function () {
                    callback(reader.result, {
                        alt: file.name,
                        title: file.name
                    });
                };

                reader.readAsDataURL(file);
            };

            input.click();
        },

        // Additional configuration options
        height: 400,
        resize: true,
        branding: false,

        // Enhanced paste options
        paste_as_text: false,
        paste_auto_cleanup_on_paste: true,
        paste_data_images: true,
        paste_webkit_styles: 'color font-size font-family',

        // Content filtering
        valid_elements: '*[*]',
        extended_valid_elements: 'script[src|async|defer|type|charset]',

        // Link options
        link_assume_external_targets: true,
        target_list: [
            {title: 'None', value: ''},
            {title: 'Same window', value: '_self'},
            {title: 'New window', value: '_blank'}
        ],

        // Table options
        table_responsive_width: true,
        table_default_attributes: {
            'class': 'table table-striped'
        },

        // Color picker options
        color_map: [
            "000000", "Black",
            "993300", "Burnt orange",
            "333300", "Dark olive",
            "003300", "Dark green",
            "003366", "Dark azure",
            "000080", "Navy Blue",
            "333399", "Indigo",
            "333333", "Very dark gray",
            "800000", "Maroon",
            "FF6600", "Orange",
            "808000", "Olive",
            "008000", "Green",
            "008080", "Teal",
            "0000FF", "Blue",
            "666699", "Grayish blue",
            "808080", "Gray",
            "FF0000", "Red",
            "FF9900", "Amber",
            "99CC00", "Yellow green",
            "339966", "Sea green",
            "33CCCC", "Turquoise",
            "3366FF", "Royal blue",
            "800080", "Purple",
            "999999", "Medium gray",
            "FF00FF", "Magenta",
            "FFCC00", "Gold",
            "FFFF00", "Yellow",
            "00FF00", "Lime",
            "00FFFF", "Aqua",
            "00CCFF", "Sky blue",
            "993366", "Red violet",
            "FFFFFF", "White",
            "FF99CC", "Pink",
            "FFCC99", "Peach",
            "FFFF99", "Light yellow",
            "CCFFCC", "Pale green",
            "CCFFFF", "Pale cyan",
            "99CCFF", "Light sky blue",
            "CC99FF", "Plum"
        ],

        // Custom color picker
        color_cols: 8,

        // Setup callback with image editing enhancements
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });

            // Add custom button for image effects
            editor.ui.registry.addButton('imageeffects', {
                text: 'Image Effects',
                icon: 'image',
                onAction: function() {
                    const selectedNode = editor.selection.getNode();
                    if (selectedNode.tagName === 'IMG') {
                        editor.windowManager.open({
                            title: 'Image Effects',
                            body: {
                                type: 'panel',
                                items: [
                                    {
                                        type: 'selectbox',
                                        name: 'filter',
                                        label: 'Filter Effect',
                                        items: [
                                            {text: 'None', value: 'none'},
                                            {text: 'Grayscale', value: 'grayscale(100%)'},
                                            {text: 'Sepia', value: 'sepia(100%)'},
                                            {text: 'Blur', value: 'blur(5px)'},
                                            {text: 'Brightness', value: 'brightness(150%)'},
                                            {text: 'Contrast', value: 'contrast(150%)'},
                                            {text: 'Hue Rotate', value: 'hue-rotate(90deg)'},
                                            {text: 'Invert', value: 'invert(100%)'},
                                            {text: 'Saturate', value: 'saturate(200%)'}
                                        ]
                                    },
                                    {
                                        type: 'slider',
                                        name: 'opacity',
                                        label: 'Opacity',
                                        min: 0,
                                        max: 100,
                                        value: 100
                                    },
                                    {
                                        type: 'input',
                                        name: 'border',
                                        label: 'Border (e.g., 2px solid #333)'
                                    },
                                    {
                                        type: 'input',
                                        name: 'shadow',
                                        label: 'Box Shadow (e.g., 0 4px 8px rgba(0,0,0,0.3))'
                                    }
                                ]
                            },
                            buttons: [
                                {
                                    type: 'cancel',
                                    text: 'Cancel'
                                },
                                {
                                    type: 'submit',
                                    text: 'Apply',
                                    primary: true
                                }
                            ],
                            onSubmit: function(api) {
                                const data = api.getData();
                                let styles = [];

                                if (data.filter && data.filter !== 'none') {
                                    styles.push(`filter: ${data.filter}`);
                                }
                                if (data.opacity < 100) {
                                    styles.push(`opacity: ${data.opacity / 100}`);
                                }
                                if (data.border) {
                                    styles.push(`border: ${data.border}`);
                                }
                                if (data.shadow) {
                                    styles.push(`box-shadow: ${data.shadow}`);
                                }

                                if (styles.length > 0) {
                                    selectedNode.style.cssText = styles.join('; ');
                                } else {
                                    selectedNode.removeAttribute('style');
                                }

                                api.close();
                            }
                        });
                    } else {
                        editor.notificationManager.open({
                            text: 'Please select an image first.',
                            type: 'warning'
                        });
                    }
                }
            });

            // Add image effects button to toolbar
            editor.on('init', function() {
                // Update toolbar to include image effects
                const toolbar = editor.getParam('toolbar');
                if (toolbar && !toolbar.includes('imageeffects')) {
                    editor.options.set('toolbar', toolbar + ' | imageeffects');
                }
            });

            // Add custom button for font preview
            editor.ui.registry.addButton('fontpreview', {
                text: 'Font Preview',
                onAction: function() {
                    editor.windowManager.open({
                        title: 'Font Preview',
                        body: {
                            type: 'panel',
                            items: [
                                {
                                    type: 'htmlpanel',
                                    html: `
                                        <div style="max-height: 300px; overflow-y: auto;">
                                            <div style="font-family: Arial; padding: 5px;">Arial - The quick brown fox jumps over the lazy dog</div>
                                            <div style="font-family: 'Times New Roman'; padding: 5px;">Times New Roman - The quick brown fox jumps over the lazy dog</div>
                                            <div style="font-family: 'Open Sans'; padding: 5px;">Open Sans - The quick brown fox jumps over the lazy dog</div>
                                            <div style="font-family: Roboto; padding: 5px;">Roboto - The quick brown fox jumps over the lazy dog</div>
                                            <div style="font-family: Poppins; padding: 5px;">Poppins - The quick brown fox jumps over the lazy dog</div>
                                            <div style="font-family: Montserrat; padding: 5px;">Montserrat - The quick brown fox jumps over the lazy dog</div>
                                            <div style="font-family: Lato; padding: 5px;">Lato - The quick brown fox jumps over the lazy dog</div>
                                            <div style="font-family: 'Playfair Display'; padding: 5px;">Playfair Display - The quick brown fox jumps over the lazy dog</div>
                                            <div style="font-family: Merriweather; padding: 5px;">Merriweather - The quick brown fox jumps over the lazy dog</div>
                                            <div style="font-family: 'Fira Code'; padding: 5px;">Fira Code - The quick brown fox jumps over the lazy dog</div>
                                        </div>
                                    `
                                }
                            ]
                        },
                        buttons: [
                            {
                                type: 'cancel',
                                text: 'Close'
                            }
                        ]
                    });
                }
            });
        }
    });
</script>

<!-- Include Google Fonts in your HTML head section -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700&family=Lato:wght@300;400;700&family=Nunito:wght@300;400;600;700&family=Source+Sans+Pro:wght@300;400;600;700&family=Raleway:wght@300;400;500;600;700&family=Ubuntu:wght@300;400;500;700&family=Playfair+Display:wght@400;700&family=Merriweather:wght@300;400;700&family=Libre+Baskerville:wght@400;700&family=Crimson+Text:wght@400;600&family=Fira+Code:wght@300;400;500&family=Source+Code+Pro:wght@300;400;500;600&family=JetBrains+Mono:wght@300;400;500;600&display=swap" rel="stylesheet">
