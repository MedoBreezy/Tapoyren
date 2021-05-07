import React, { useState, useEffect } from "react";
import ReactDOM from 'react-dom'

import axios from 'axios'

import Editor from '../../components/Editor'

import config from '../../config'

import { uploadImage } from '../../helpers'

const _target = document.getElementById('react-package-description');

let _initialValue
if (_target) _initialValue = _target.dataset.value

function PackageDescription() {
   const [state, setState] = useState({
      description: _initialValue
   });


   return (
      <>
         <Editor height={250} value={state['description']} handleChange={(content, editor) => setState({ ...state, description: content })} />
         <input type="hidden" name="description" value={state.description} />
      </>
   );
}

if (_target) ReactDOM.render(<PackageDescription />, _target);

