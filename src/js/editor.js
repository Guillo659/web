const editor = new EditorJS({
  holder: 'editorjs',

  placeholder:"Type here", 
  tools: {
    header: Header,
    image: SimpleImage,
    list: List,
    quote: Quote,
    inlineCode: InlineCode,
    linkTool: LinkTool,
    embed: Embed,
  },
  tools: {
      header: {
        class: Header,
        config: {
          placeholder: 'Enter a header',
          levels: [2, 3, 4],
          defaultLevel: 2
        }
      },
      image: SimpleImage,
      list: {
          class: List,
          inlineToolbar: true
        },
        embed: {
          class: Embed,
          inlineToolbar: true,
          config: {
            services: {
              youtube: true,
              coub: true,
              facebook: true
            }
          }
        },
        /*linkTool: {
          class: LinkTool,
          config: {
            endpoint: 'http://localhost/model/set_link.php', // Your backend endpoint for url data fetching,
          }
        },*/
        quote: {
          class: Quote,
          inlineToolbar: true,
          shortcut: 'CMD+SHIFT+O',
          config: {
            quotePlaceholder: 'Enter a quote',
            captionPlaceholder: 'Quote\'s author',
          },
        },
        attaches: {
          class: AttachesTool,
          config: {
            endpoint: 'http://localhost/model/set_attaches.php'
          }
        },
        inlineCode: {
          class: InlineCode,
          shortcut: 'CMD+SHIFT+M',
        },
        image: {
          class: ImageTool,
          config: {
              /**
               * Custom uploader
               */
              uploader: {
                  /**
                   * Upload file to the server and return an uploaded image data
                   * @param {File} file - file selected from the device or pasted by drag-n-drop
                   * @return {Promise.<{success, file: {url}}>}
                   */
                  uploadByFile(file){
                      // Create a FormData object
                      const formData = new FormData();
                      formData.append('file', file);
                      
                      // Send the FormData object to the server
                      return fetch('../model/set_article_img.php', {
                          method: 'POST',
                          body: formData
                      })
                      .then(response => response.json())
                      .then(data => {
                          return data;
                      });
                  },
                  
                  uploadByUrl(url){
                      // Send the URL to the server
                      return fetch('../model/set_article_img.php', {
                          method: 'POST',
                          headers: {
                              'Content-Type': 'application/json'
                          },
                          body: JSON.stringify({url: url})
                      })
                      .then(response => response.json())
                      .then(data => {
                          return data;
                      });
                  }
              } // Cierre del objeto uploader
          } // Cierre del objeto config
      } // Cierre del objeto image
    },
});

 /* let dboton = document.querySelector('button')

 dboton.addEventListener('click', function () {
  editor.save().then((outputData) =>{
    console.log("Article Data: ", outputData)    
  }).catch((error) => {
    console.log("Saving Failed", error)
  })
 }) */