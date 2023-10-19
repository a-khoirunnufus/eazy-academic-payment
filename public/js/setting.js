const _activeSchoolYear = {
    data: null,
    storageName: "activeSchoolYear",
    loaded: false,
    load: async function(){
        this.data = await this.loadFromServer()

        this.loaded = true

        this.mountInfo()
    },
    loadFromServer: function(){
        return new Promise((resolve) => {{
            $.get(_baseURL + '/api/data/school-year/active', function(data){
                resolve(data.data)
            })
        }})
    },
    loadFromCache: function(){
        let data = localStorage.getItem(this.storageName)
        if(data == null)
            return null

        return JSON.parse(data)
    },
    clear: function(){
        localStorage.removeItem(this.storageName)
    },
    mountInfo: function(){
        let info = ''
        if(this.data == null){
            info = 'Tidak ada semester aktif'
            $(".school-year-info .small-info").attr({'class': 'btn bg-label-danger rounded-pill btn-icon small-info'})
            $(".school-year-info .large-info").attr({'class': 'large-info btn bg-label-danger'})
        } else {
            info = `Semester ${['Ganjil', 'Genap'][parseInt(this.data.msy_semester) - 1]} ${this.data.msy_year}`
            $(".school-year-info .small-info").attr({'class': 'btn bg-label-info rounded-pill btn-icon small-info'})
            $(".school-year-info .large-info").attr({'class': 'large-info btn bg-label-info'})
        }

        $(".school-year-info .small-info").attr({'data-bs-original-title': info})
        $(".school-year-info .large-info span").text(info)
    },
    waitUntilLoaded: function(){
        return new Promise((resolve) => {
            let interval = setInterval(() => {
                if(this.loaded){
                    clearInterval(interval)
                    resolve()
                }
            }, 50)
        })
    }
}
