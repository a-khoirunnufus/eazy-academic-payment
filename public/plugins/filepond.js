
class Filepond {
    
    constructor(elementSelector)
    {
        this.elementSelector = elementSelector

        this.baseUrl = _baseURL

        this.allowDeleteFile = true

        this.uploadUrl = '/api/resources/upload'

        this.deleteUrl = '/api/resources/'

        this.payloadName = 'file'

        this.onUploadedCallback = null

        this.initInstance()

        this.initServer()
    }

    initInstance()
    {
        $(this.elementSelector).filepond()
        $(this.elementSelector).on('FilePond:processfile', (e) => {
            if(this.onUploadedCallback !== null)
                this.onUploadedCallback(e.detail.file)
        });
    }

    allowMultiple(is)
    {
        $(this.elementSelector).filepond('allowMultiple', is)
    }

    initServer()
    {
        $(this.elementSelector).filepond('server', {
            process: {
                url: this.baseUrl + this.uploadUrl,
                ondata: (formData) => {
                    let file
                    // get file
                    for (const pair of formData.entries()) {
                        if(typeof(pair[1]) != 'string')
                            file = pair[1]
                    }
                    // reset form data
                    for (var key of formData.keys()) {
                        formData.delete(key)
                    }
                    // append file
                    formData.append(this.payloadName, file)

                    return formData
                }
            },
            revert: (resourceId, load, error) => {
                if(this.allowDeleteFile)
                    this.deleteResource(resourceId)
            },
            headers: {'X-CSRF-TOKEN': _csrfToken}    
        });  
    }

    deleteResource(resourceId)
    {
        $.ajax({
            url: this.baseUrl + this.deleteUrl + resourceId,
            type: 'DELETE',
            success: function(data) {

            }
        })
    }

    getFiles(){
        return $(this.elementSelector).filepond('getFiles')
    }

    clearInput(removeResource = false)
    {
        const files = this.getFiles()

        if(!removeResource)
            this.allowDeleteFile = false

        $(this.elementSelector).filepond('removeFiles', files )

        if(!removeResource)
            this.allowDeleteFile = true
    }

    getFilesId()
    {
        let files = this.getFiles()
        return files.filter((item) => {
            return item.status == 5
        }).map((item) => {
            return item.serverId
        })
    }

    getFileId()
    {
        let files = this.getFilesId()
        if(files.length == 0)
            return null
        else
            return files[0]
    }

    allFileUploaded()
    {
        let files = this.getFiles()
        let incompleteUploadedFile = files.filter((item) => {
            return item.status != 5
        })

        return incompleteUploadedFile.length == 0
    }

    onUploaded(callback)
    {
        this.onUploadedCallback = callback
    }

}