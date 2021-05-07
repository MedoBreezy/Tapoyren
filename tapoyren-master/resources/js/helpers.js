import axios from 'axios'
import config from './config'

export const uploadImage = (image, callback = null) => {
   const data = new FormData()
   data.append('file', image)
   axios.post(config.api + '/upload/image', data).then(res => {
      if (callback) callback(res.data)
   })
}

export const uploadVideo = (video, callback = null) => {
   const data = new FormData()
   data.append('file', video)
   axios.post(config.api + '/upload/video', data).then(res => {
      if (callback) callback(res.data)
   })
}

export const uploadDoc = (doc, callback = null) => {
   const data = new FormData()
   data.append('file', doc)
   axios.post(config.api + '/upload/doc', data).then(res => {
      if (callback) callback(res.data)
   })
}
