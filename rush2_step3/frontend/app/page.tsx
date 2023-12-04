"use client"

import styles from './page.module.css'
import {TextField} from '@mui/material'
import DragAndDrop from './_components/dragAndDrop'
import {useState} from 'react'
import {ToastContainer, toast} from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import {useQuery} from "@tanstack/react-query";
import axios from 'axios';

export default function Home() {
    const [nameOfFile, setNameOfFile] = useState<string>('');
    const [onClickSend, setOnClickSend] = useState<any>(null);
    const [onClickDownloadTarball, setOnClickDownloadTarball] = useState<any>(null);
    const try_to_connect_to_api = async () => {
        return await axios.get(process.env.NODE_ENV === "development" ? "http://localhost:8000/api/ping" : "http://zuux.fr/api/ping")
            .then(({data}) => {
                toast("ping serveur réussi", {
                    type: 'success'
                })
                return data
            })
            .catch((e) => {
                toast("ping serveur impossible", {
                    type: "error"
                })
                return e;
            })
    }

    const downloadFile = async () => {
        try {
            const response = await axios.get(process.env.NODE_ENV === "development" ? "http://localhost:8000/api/tar/download" : "http://zuux.fr/api/tar/download", {
                responseType: 'blob',
            });

            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', nameOfFile.length > 0 ? nameOfFile + '.mytar' : 'output.mytar');
            document.body.appendChild(link);
            link.click();
            toast("Téléchargement de votre archive réussi.");
        } catch (error) {
            toast("Merci de bien vouloir générer une tarball avant téléchargement.", {
                type: "error"
            })
        }
    };

    useQuery({queryKey: ["first_connexion"], queryFn: try_to_connect_to_api})

    return (
        <main className={styles.main}>
            <ToastContainer
                position="top-right"
                autoClose={5000}
                hideProgressBar={false}
                newestOnTop={false}
                closeOnClick
                rtl={false}
                pauseOnFocusLoss
                draggable
                pauseOnHover
                theme="light"
            />
            <div className={styles.description}>
                <p className='font-bold text-md'>
                    Rush 2 Web@cademie By Epitech
                </p>
                <div>
                    <a
                        href="https://github.com/EpitechWebAcademiePromo2025/W-WEB-024-LIL-1-1-rush2-judikael2.bellance/tree/main"
                        target="_blank"
                        rel="noopener noreferrer"
                        className='font-bold text-md'
                    >
                        By{' '}
                        Med, Enzo And Judikaël
                    </a>
                </div>
            </div>

            <div className='flex'>
                <div className='m-2 flex-initial w-100'>
                    <div>
                        <h3 className='font-bold text-2xl my-2'>Utiliser le drag and drop de fichier afin d&apos;envoyer
                            vos fichers </h3>
                    </div>
                    <div>
                        <TextField
                            fullWidth
                            id="filename"
                            className='my-2'
                            value={nameOfFile}
                            variant='outlined'
                            onChange={e => setNameOfFile(e.target.value)}
                            label="Choisissez le nom de votre tarball"/>
                    </div>
                    <div className='p-1 flex justify-around'>
                        <button
                            onClick={setOnClickSend}
                            className='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded m-1'>Générer
                            la tarball
                        </button>
                        <button
                            onClick={downloadFile}
                            className='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded m-1'>Télécharger
                            la tarball
                        </button>
                    </div>
                    <div>
                        <DragAndDrop onClickSend={onClickSend} onClickDownloadTarball={onClickDownloadTarball} setOnClickSend={setOnClickSend}/>
                    </div>
                </div>
            </div>

            <div className={styles.grid}>
                <a
                    href="https://github.com/EpitechWebAcademiePromo2025/W-WEB-024-LIL-1-1-rush2-judikael2.bellance/tree/main/rush2_step1"
                    className={styles.card}
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <h2 className='font-bold text-xl'>
                        Étape 1<span>-&gt;</span>
                    </h2>
                    <p>Création d&apos;une tarball.</p>
                </a>

                <a
                    href="https://github.com/EpitechWebAcademiePromo2025/W-WEB-024-LIL-1-1-rush2-judikael2.bellance/tree/main/rush2_step2"
                    className={styles.card}
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <h2 className='font-bold text-xl'>
                        Étape 2 <span>-&gt;</span>
                    </h2>
                    <p>Création de la décompression d&apos;une tarball.</p>
                </a>

                <a
                    href="https://github.com/EpitechWebAcademiePromo2025/W-WEB-024-LIL-1-1-rush2-judikael2.bellance/tree/main/rush2_step3"
                    className={styles.card}
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <h2 className='font-bold text-xl'>
                        Étape 3 <span>-&gt;</span>
                    </h2>
                    <p>Ceci est le site que vous visitez actuellement qui consiste à être un site web qui créé et
                        décompresse des tarballs.</p>
                </a>

                <a
                    href="https://github.com/EpitechWebAcademiePromo2025/W-WEB-024-LIL-1-1-rush2-judikael2.bellance/tree/main/rush2_bonus"
                    className={styles.card}
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <h2 className='font-bold text-xl'>
                        Bonus <span>-&gt;</span>
                    </h2>
                    <p>
                        Amélioration de l&apos;algorithme de compression.
                    </p>
                </a>
            </div>
            <footer>
                <p className='font-bold my-2'>
                    {new Date().getFullYear()} © Made By Med, Enzo And Judikaël
                </p>
            </footer>
        </main>
    )
}