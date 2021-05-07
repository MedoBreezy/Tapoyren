import React, { useState, useEffect, useRef } from "react";
import ReactDOM from 'react-dom'

import axios from 'axios'

import Editor from '../../components/Editor'

import config from '../../config'

let course_id = null

const _target = document.getElementById('react-add-course-data');
if (_target) {
   const userApiToken = _target.dataset.token
   course_id = _target.dataset.courseId
   axios.defaults.headers.common['Authorization'] = "Bearer " + userApiToken;
   axios.defaults.headers.post['Accept'] = 'application/json';
}

import { uploadDoc } from '../../helpers'

function AddCourseData() {
   const videoPreviewRef = useRef()
   const resourceUploadRef = useRef()

   const [data, setData] = useState({
      course: {},
   })

   const [state, setState] = useState({
      resourceName: '',
      resourceFile: null,
      resources: [],

      sectionName: '',
      sections: [],

      videoName: '',
      videoPreview: false,
      vimeoVideoId: '',
   });

   useEffect(() => {

      axios.get(config.api + `/course/${course_id}/data`).then(res => {
         setData({ ...data, course: res.data.course })
      })

   }, [])

   const saveCourse = () => {
      const courseData = new FormData()

      courseData.append('resources', JSON.stringify(state.resources))
      courseData.append('sections', JSON.stringify(state.sections))

      axios.post(config.api + `/course/${course_id}/add_data`, courseData).then(res => {
         console.log(res.data)
         alert('Kurs məlumatları əlavə edildi')
         window.location.replace(config.host + `/admin/courses`)
      })
   }

   const handleResourceFile = e => {
      if (e.target.files.length === 1) {
         console.log('uploading doc', resourceUploadRef.current)
         uploadDoc(e.target.files[0], data => {
            setState({ ...state, resourceFile: data.path })
         })
      }
   }

   const handleResourceAdd = () => {
      const newResource = {
         title: state.resourceName,
         file: state.resourceFile
      }
      setState({
         ...state,
         resources: [...state.resources, newResource],
         resourceName: '',
         resourceFile: null
      })
      resourceUploadRef.current.value = ''
   }

   const removeResource = index => {
      const { resources } = state
      resources.splice(index, 1)
      setState({ ...state, resources })
   }

   const handleSectionAdd = e => {
      if (e.key === 'Enter') {
         const newSection = {
            title: state.sectionName,
            videos: []
         }
         setState({
            ...state,
            sectionName: '',
            sections: [...state.sections, newSection]
         })
      }
   }

   const removeSection = index => {
      const { sections } = state
      sections.splice(index, 1)
      setState({ ...state, sections })
   }

   const handleVideoAdd = sectionIndex => {
      const { sections } = state
      const section = sections[sectionIndex]
      const newVideo = {
         title: state.videoName,
         vimeoVideoId: state.vimeoVideoId,
         preview: state.videoPreview
      }
      section.videos.push(newVideo)

      sections[sectionIndex] = section

      setState({
         ...state,
         videoName: '',
         videoPreview: false,
         vimeoVideoId: '',
         sections
      })
      videoPreviewRef.current.checked = false
   }

   const handleVideoRemove = (sectionIndex, videoIndex) => {
      const { sections } = state
      const section = sections[sectionIndex]
      const { videos } = section
      videos.splice(videoIndex, 1)
      section.videos = videos
      sections[sectionIndex] = section
      setState({ ...state, sections })
   }


   return (
      <div style={{ width: '80%', margin: '0 auto', padding: 15, overflowY: 'scroll' }}>

         <h2>{data.course.title}</h2>
         <hr />

         <div className="mb-8pt">
            <h4><b>Resources</b></h4>
         </div>
         {state.resources.map((resource, i) => {
            return <div key={i}>
               {resource.title}
               <button
                  className="btn btn-danger btn-sm ml-8pt"
                  onClick={() => removeResource(i)}>x</button>
            </div>
         })}

         <div className="d-flex align-items-center row">

            <div className="col-5 d-flex align-items-center mr-8pt">
               <div className="mr-8pt">Title:</div>
               <input
                  className="form-control"
                  type="text"
                  value={state.resourceName}
                  onChange={e => setState({ ...state, resourceName: e.target.value })}
               />
            </div>

            <div className="col-5 d-flex align-items-center mr-8pt">
               <div className="mr-8pt">File:</div>
               <input
                  ref={resourceUploadRef}
                  className="form-control"
                  type="file"
                  onChange={handleResourceFile}
               />
            </div>

            <button
               className="btn btn-danger btn-sm ml-8pt"
               onClick={handleResourceAdd}>+</button>
         </div>

         <hr />

         <div className="mb-8pt">
            <h4><b>Sections</b></h4>
         </div>
         {state.sections.map((section, sectionIndex) => {
            return <div key={sectionIndex}>
               <div>
                  {section.title}
                  <button
                     className="btn btn-danger btn-sm ml-8pt"
                     onClick={() => removeSection(sectionIndex)}>x</button>
               </div>
               <div style={{ background: '#fff', borderRadius: 4, padding: 10 }}>
                  <div style={{ padding: 15 }}>
                     {section.videos.map((video, videoIndex) => {
                        return <div key={videoIndex} className="mb-8pt">
                           <span>{video.title}</span>
                           <button
                              className="btn btn-danger btn-sm ml-8pt"
                              onClick={() => handleVideoRemove(sectionIndex, videoIndex)}>x</button>
                        </div>
                     })}
                     <div className="d-flex align-items-center">
                        <div className="mr-8pt">
                           <span>Preview:</span>
                           <input
                              ref={videoPreviewRef}
                              className="form-control"
                              type="checkbox"
                              onClick={e => setState({ ...state, videoPreview: e.target.checked })}
                           />
                        </div>
                        <div className="mr-8pt">
                           <span>Title:</span>
                           <input
                              className="form-control"
                              type="text"
                              value={state.videoName}
                              onChange={e => setState({ ...state, videoName: e.target.value })}
                           />
                        </div>
                        <div className="mr-8pt">
                           <span>Vimeo Video ID:</span>
                           <input
                              className="form-control"
                              type="text"
                              value={state.vimeoVideoId}
                              onChange={e => setState({ ...state, vimeoVideoId: e.target.value })}
                           />
                        </div>
                        <button
                           className="btn btn-danger btn-sm ml-8pt"
                           onClick={() => handleVideoAdd(sectionIndex)}>+</button>
                     </div>
                  </div>
               </div>
            </div>
         })}
         <input
            className="form-control"
            type="text"
            value={state.sectionName}
            onChange={e => setState({ ...state, sectionName: e.target.value })}
            onKeyDown={e => handleSectionAdd(e)}
         />

         <br />

         <button onClick={() => saveCourse()} className="btn btn-primary btn-lg btn-block">Add Course</button>



      </div>
   );
}

if (_target) ReactDOM.render(<AddCourseData />, _target);

