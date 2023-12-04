"use client";

import {useRef, useEffect, useState} from "react";
import {toast} from "react-toastify";
import {useMutation} from "@tanstack/react-query";
import axios from 'axios';

export default function DragAndDrop({onClickSend, onClickDownloadTarball, setOnClickSend}: { onClickSend: any, onClickDownloadTarball: any, setOnClickSend: Function}) {
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

    const insert_file_to_server = async (files: any) => {
        if (files.length === 0) {
            toast("Aucun fichier n'a été ajouté, annulation de l'envoie du formulaire.", {
                type: 'error'
            })
            return;
        } else {
            const fd = new FormData();

            files.forEach((file: any, index: any) => {
                fd.append(`files[${index}]`, file);
            });

            return await axios.post(process.env.NODE_ENV === "development" ? "http://localhost:8000/api/tar/make" : "http://zuux.fr/api/tar/make", fd, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            })
                .then(({data}) => {
                    toast("Votre tarball à bien été générer", {
                        type: 'success'
                    })
                    return data
                })
                .catch(e => {
                    console.log(e)
                    toast("erreur lors de la génération de votre tarball", {
                        type: 'error'
                    })
                })
        }
    }

    const mutation = useMutation({
        mutationFn: insert_file_to_server,
        onError: () => {
            toast("Une erreur est survenue lors de l'envoie de vos fichier")
        }
    })

    useEffect(() => {
        if (onClickSend !== null) {
            mutation.mutate(files)
            setOnClickSend(null); // prevent second send without anything
        }
    }, [onClickSend])

    function handleChange(e: any) {
        e.preventDefault();
        console.log("File has been added");
        if (e.target.files && e.target.files[0]) {
            for (let i = 0; i < e.target.files["length"]; i++) {
                setFiles((prevState: any) => [...prevState, e.target.files[i]]);
            }
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
        toast(`Le fichier ${fileName} à bien été retirer de la liste`)
    }

    function openFileExplorer() {
        inputRef.current.value = "";
        inputRef.current.click();
    }

    function removeAllFiles() {
        if (window.confirm("Êtes vous sûr de vouloir supprimer de la liste tous les fichiers importé ?")) {
            setFiles([]);
            toast("Les fichiers précédement envoyé ont bien été vider de la liste")
        }
    }

    return (
        <div className="flex items-center justify-center w-full my-2">
            <form
                className={`${dragActive ? "bg-blue-400" : "bg-blue-500"
                }  p-4 w-100 rounded-lg border-solid border-2 border-black text-center borderff flex flex-col items-center w-100 justify-center`}
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

                <p className="font-bold">
                    Drag & Drop vos dossiers ou{" "}
                    <span
                        className="font-bold text-red-400 cursor-pointer"
                        onClick={openFileExplorer}
                    >
            <u>Séléctionner vos dossiers</u>
          </span>{" "}
                    pour les transformer en tarball
                </p>

                <div className="flex flex-col items-center p-3 overflow-auto h-48 min-h-full">
                    {files.map((file: any, idx: any) => (
                        <div key={idx} className="flex flex-row flex-gap-1 justify-between space-x-5 ">
                            <span className="flex">{file.name}</span>
                            <span
                                style={{
                                    padding: '.5px',
                                    cursor: 'pointer'
                                }}
                                className="bg-red-500 flex hover:bg-red-700 text-white font-bold rounded m-1"
                                onClick={() => removeFile(file.name, idx)}
                            >
                Supprimer
              </span>
                        </div>
                    ))}
                </div>

                {files.length > 0 ? <span
                    style={{
                        padding: '2px',
                        cursor: 'pointer'
                    }}
                    className="bg-red-500 hover:bg-red-700 text-white font-bold rounded m-1"
                    onClick={() => removeAllFiles()}
                >
          Supprimer tous les fichiers
        </span> : ''}
            </form>

        </div>
    );
}