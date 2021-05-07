import React, { useState, useEffect } from "react";
import ReactDOM from 'react-dom'

import axios from 'axios'

import Editor from '../../components/Editor'

import config from '../../config'

import { uploadImage } from '../../helpers'

const _target = document.getElementById('react-update-instructor');
let instructor_id = null
if (_target) {
   const userApiToken = _target.dataset.token
   instructor_id = _target.dataset.instructor
   axios.defaults.headers.common['Authorization'] = "Bearer " + userApiToken;
   axios.defaults.headers.post['Accept'] = 'application/json';
}

function UpdateInstructor() {

   const [data, setData] = useState({
      getData: false,
      instructorData: {},
   })

   const [state, setState] = useState({
      avatar_url: null,
      bio: ''
   });

   useEffect(() => {
      if (!data.getData) {
         if (Object.keys(data.instructorData).length === 0) axios.get(config.api + `/instructor/${instructor_id}`).then(res => {
            const { instructor } = res.data
            setData({ ...data, instructorData: instructor })
            setState({ ...state, avatar_url: instructor.avatar_url, bio: instructor.bio })
         })

         setData({ ...data, getData: true })
      }
   }, [data.getData])

   const updateInstructor = () => {
      const instructorData = new FormData()

      if (state.avatar_url) instructorData.append('avatar_url', state.avatar_url)
      if (state.bio !== null && state.bio !== '') instructorData.append('bio', state.bio)

      axios.post(config.api + `/instructor/${instructor_id}/update`, instructorData).then(res => {
         Swal.fire('Yeniləndi!', '', 'success')
         setTimeout(() => {
            window.location.replace(config.host + `/admin/instructors`)
         }, 2000);
      })
   }

   const onThumbnailChange = e => {
      if (e.target.files.length > 0) {
         uploadImage(e.target.files[0], res => {
            setState({ ...state, avatar_url: res.path })
         })
      }
   }


   return (
      <div style={{ width: '80%', margin: '0 auto', padding: 15 }}>

         {data.instructorData.avatar_url !== null ? (
            <img src={data.instructorData.avatar_url} style={{ width: 80, height: 80, borderRadius: '50%', objectFit: 'contain', margin: 10 }} />
         ) : state.avatar_url !== null && (
            <img src={state.avatar_url} style={{ width: 80, height: 80, borderRadius: '50%', objectFit: 'contain', margin: 10 }} />
         )}
            Thumbnail: <input type="file" onChange={e => onThumbnailChange(e)} /><br />

         <br />
         <h5>About</h5>
         <Editor height={250} value={state.bio} handleChange={(content, editor) => setState({ ...state, bio: content })} />
         <br />


         <button onClick={() => updateInstructor()} className="btn btn-primary btn-lg btn-block">Update</button>



      </div>
   );
}

if (_target) ReactDOM.render(<UpdateInstructor />, _target);

