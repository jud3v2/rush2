"use client"

import styles from './page.module.css'
import { TextField } from '@mui/material'
import DragAndDrop from './_components/dragAndDrop'
import { useState } from 'react'
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

export default function Home() {
  const [nameOfFile, setNameOfFile] = useState('');
  
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
            <h3 className='font-bold text-2xl my-2'>Utiliser le drag and drop de fichier afin d&apos;envoyer vos fichers </h3>
          </div>
          <div>
            <TextField 
            fullWidth 
            id="filename" 
            className='my-2'
            value={nameOfFile}
            variant='outlined' 
            onChange={e => setNameOfFile(e.target.value)}
            label="Choisissez le nom de votre tarball"  />
          </div>
          <div className='p-1 flex justify-around'>
            <button className='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded m-1'>Générer la tarball</button>
            <button className='bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded m-1'>Télécharger la tarball</button>
          </div>
          <div>
            <DragAndDrop />
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
          <p>Ceci est le site que vous visitez actuellement qui consiste à être un site web qui créé et décompresse des tarballs.</p>
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
          <p className='font-bold'>
            {new Date().getFullYear()} © Made By Med, Enzo And Judikaël
          </p>
      </footer>
    </main>
  )
}