import React, { useState, useEffect, useRef } from "react";
import ReactDOM from 'react-dom'

import axios from 'axios'

import Editor from '../../components/Editor'

import config from '../../config'

let language_id = null

const _target = document.getElementById('react-language-add-translations');
if (_target) {
    const userApiToken = _target.dataset.token
    language_id = _target.dataset.languageId
    axios.defaults.headers.common['Authorization'] = "Bearer " + userApiToken;
    axios.defaults.headers.post['Accept'] = 'application/json';
}


function LanguageTranslations() {
    const [data, setData] = useState({
        language: {},
        string_keys: [],
        text_keys: [],
        string_translations: [],
        text_translations: []
    })

    const [state, setState] = useState({

    });

    useEffect(() => {

        axios.get(config.api + `/language/${language_id}/data`).then(res => {

            setData({
                ...data,
                language: res.data.language,
                string_keys: res.data.keys.strings,
                text_keys: res.data.keys.texts,
                string_translations: res.data.translations.strings,
                text_translations: res.data.translations.texts,
            })

        })

    }, [])

    const getValue = (key, type) => {
        let found = ''

        console.log(data)

        if (type === 'string' && data.string_translations.length > 0) data.string_translations.forEach(translation => {
            if (translation.key === key) found = translation.value
        })
        else if (type === 'text' && data.text_translations.length > 0) data.text_translations.forEach(translation => {
            if (translation.key === key) found = translation.value
        })

        return found
    }

    const setValue = (key, value, type) => {
        let translations
        if (type === 'string') translations = data.string_translations
        else if (type === 'text') translations = data.text_translations

        let check = false
        translations.forEach(translation => {
            if (translation.key === key) check = true
        })

        if (check) translations = translations.map(translation => {
            if (translation.key === key) return {
                key: key,
                value: value
            }
            else return translation
        })
        else translations.push({ key, value })

        if (type === 'string') setData({ ...data, string_translations: translations })
        else if (type === 'text') setData({ ...data, text_translations: translations })

    }

    const save = () => {

        axios.post(config.api + `/language/${language_id}/translations`, {
            string_translations: data.string_translations,
            text_translations: data.text_translations
        }).then(res => {
            Swal.fire('Language updated successfully!', '', 'success')
        }).catch(err => {
            Swal.fire('Error updating language!', '', 'error')
        })
    }


    return (
        <div style={{ width: '80%', margin: '0 auto', padding: 15 }}>
            <h2>{data.language.title}</h2>
            <hr />
            {data.string_keys.length > 0 && data.string_keys.map((key, i) => {
                return <div key={i} style={{ display: 'flex', alignItems: 'center', margin: '5px 0' }}>
                    <input type="text" placeholder={key.name} className="form-control mr-2" disabled />
                    <input type="text" placeholder="Value" value={getValue(key.key, 'string')} onChange={e => setValue(key.key, e.target.value, 'string')} className="form-control" />
                </div>
            })}
            <br />
            {data.text_keys.length > 0 && data.text_keys.map((key, i) => {
                return <div key={i} style={{ display: 'flex', alignItems: 'center', margin: '5px 0' }}>
                    <input type="text" placeholder={key.name} className="form-control mr-2" style={{ width: '25%' }} disabled />
                    <Editor menubar={false} height={250} value={getValue(key.key, 'text')} handleChange={(content, editor) => setValue(key.key, content, 'text')} />
                </div>
            })}
            <br />
            <button className="btn btn-success" onClick={save}>Save</button>
        </div>
    );
}

if (_target) ReactDOM.render(<LanguageTranslations />, _target);

