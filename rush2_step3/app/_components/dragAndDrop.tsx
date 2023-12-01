"use client";

import { useRef, useEffect, useState } from "react";

export default function DragAndDrop() {
    const [dragActive, setDragActive] = useState<boolean>(false);
    const inputRef = useRef<any>(null);
    const [files, setFiles] = useState<any>([]);
    useEffect(() => {
        if (inputRef.current !== null) {
            // 2. set attribute as JS does
            inputRef.current.setAttribute("directory", "");
            inputRef.current.setAttribute("webkitdirectory", "");
        }
    // 3. monitor change of your ref with useEffect
    }, [inputRef]);

    function handleChange(e: any) {
        e.preventDefault();
        console.log("File has been added");
        if (e.target.files && e.target.files[0]) {
          for (let i = 0; i < e.target.files["length"]; i++) {
            setFiles((prevState: any) => [...prevState, e.target.files[i]]);
          }
        }
      }
    
      function handleSubmitFile(e: any) {
        if (files.length === 0) {
          // no file has been submitted
        } else {
          // write submit logic here
        }
      }
    
      function handleDrop(e: any) {
        e.preventDefault();
        e.stopPropagation();
        setDragActive(false);
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
          for (let i = 0; i < e.dataTransfer.files["length"]; i++) {
            setFiles((prevState: any) => [...prevState, e.dataTransfer.files[i]]);
          }
        }
      }
    
      function handleDragLeave(e: any) {
        e.preventDefault();
        e.stopPropagation();
        setDragActive(false);
      }
    
      function handleDragOver(e: any) {
        e.preventDefault();
        e.stopPropagation();
        setDragActive(true);
      }
    
      function handleDragEnter(e: any) {
        e.preventDefault();
        e.stopPropagation();
        setDragActive(true);
      }
    
      function removeFile(fileName: any, idx: any) {
        const newArr = [...files];
        newArr.splice(idx, 1);
        setFiles([]);
        setFiles(newArr);
      }
    
      function openFileExplorer() {
        inputRef.current.value = "";
        inputRef.current.click();
      }
    
    return (
        <div className="flex items-center justify-center w-full my-2">
      <form
        className={`${
          dragActive ? "bg-blue-400" : "bg-blue-100"
        }  p-4 w-100 rounded-lg text-center border flex flex-col items-center w-100 justify-center`}
        onDragEnter={handleDragEnter}
        onSubmit={(e) => e.preventDefault()}
        onDrop={handleDrop}
        onDragLeave={handleDragLeave}
        onDragOver={handleDragOver}
      >

       <input
          placeholder="fileInput"
          className="hidden"
          ref={inputRef}
          type="file"
          multiple={true}
          onChange={handleChange}
        />

        <p>
          Drag & Drop vos fichier ou{" "}
          <span
            className="font-bold text-blue-600 cursor-pointer"
            onClick={openFileExplorer}
          >
            <u>Séléctionner vos fichiers</u>
          </span>{" "}
          pour téléchargement
        </p>

      <div className="flex flex-col items-center p-3 overflow-auto h-24 min-h-full">
          {files.map((file: any, idx: any) => (
            <div key={idx} className="flex flex-row space-x-5 ">
              <span>{file.name}</span>
              <span
                className="text-red-500 cursor-pointer"
                onClick={() => removeFile(file.name, idx)}
              >
                remove
              </span>
            </div>
          ))}
        </div>
 
      </form>
    </div>
    );
}