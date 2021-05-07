import React, { useState, useEffect } from "react";
import ReactDOM from 'react-dom'

import axios from 'axios'

import Editor from '../../components/Editor'

import config from '../../config'

const _target = document.getElementById('react-update-faq');
let faq_id = null
if (_target) {
    const userApiToken = _target.dataset.token
    faq_id = _target.dataset.faq
    axios.defaults.headers.common['Authorization'] = "Bearer " + userApiToken;
    axios.defaults.headers.post['Accept'] = 'application/json';
}

function UpdateFaq() {

    const [data, setData] = useState({
        getData: false,
        faqData: {},
        languages: []
    })

    const [state, setState] = useState({});

    useEffect(() => {
        if (!data.getData) {
            if (data.languages.length === 0) axios.get(config.api + '/languages/list').then(res => {
                if (res.data.languages.length > 0) setData({ ...data, languages: [...res.data.languages] })
            })
            if (Object.keys(data.faqData).length === 0) axios.get(config.api + `/faq/${faq_id}`).then(res => {
                setData({ ...data, faqData: res.data.faq })
            })

            setData({ ...data, getData: true })
        }
        if (data.getData) {
            data.languages.forEach(lang => {
                const map = []

                Object.keys(data.faqData).forEach(langKey => {
                    map[`title_${langKey}`] = data.faqData[langKey].title
                    map[`description_${langKey}`] = data.faqData[langKey].description
                })

                setState({ ...state, ...map })

            })
        }
    }, [data.getData])

    const updateFaq = () => {
        const faqData = new FormData()

        data.languages.forEach(lang => {
            const key_title = 'title_' + lang.slug
            const key_description = 'description_' + lang.slug
            faqData.append(key_title, state[key_title])
            faqData.append(key_description, state[key_description])
        })

        axios.post(config.api + `/faq/${faq_id}/update`, faqData).then(res => {
            Swal.fire('Yeniləndi!', '', 'success')
            setTimeout(() => {
                window.location.replace(config.host + `/admin/faq`)
            }, 2000);
        })
    }


    return (
        <div style={{ width: '80%', margin: '0 auto', padding: 15 }}>

            {data.languages.map((lang, i) => {
                return <div key={i}>

                    <h3>{lang.title}</h3>
                    <br />

                    <input
                        className="form-control"
                        type="text" placeholder="Title"
                        value={state['title_' + lang.slug] || ''}
                        onChange={e => setState({ ...state, ['title_' + lang.slug]: e.target.value })}
                    />
                    <br />


                    <br />
                    <h5>Description</h5>
                    <Editor height={250} value={state['description_' + lang.slug]} handleChange={(content, editor) => setState({ ...state, ['description_' + lang.slug]: content })} />
                    <br />

                </div>
            })}




            <button onClick={() => updateFaq()} className="btn btn-primary btn-lg btn-block">Update</button>



        </div>
    );
}

if (_target) ReactDOM.render(<UpdateFaq />, _target);

