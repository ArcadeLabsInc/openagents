// @ts-nocheck

import { NavLayout } from "@/Layouts/NavLayout";
import { useRef } from 'react'
import { Canvas, useFrame } from '@react-three/fiber'
import { useGLTF, Stage, Grid, OrbitControls, Environment } from '@react-three/drei'
import { EffectComposer, Bloom, ToneMapping } from '@react-three/postprocessing'
import { easing } from 'maath'
import { Button } from "@/Components/ui/button";
import { Link } from "@inertiajs/react";

function Splash() {
  return (
    <>
      <div className="pointer-events-none absolute top-0 left-0 w-full h-full flex flex-col justify-center items-center" style={{ zIndex: 9999 }}>
        <h1 className="-mt-16 text-7xl font-black uppercase tracking-tight">Make AI <span className="">cool</span> again</h1>
        <h3 className="mt-6 text-2xl font-light tracking-wide">OpenAgents is the community platform for building AI agents.</h3>
        <Link href="/agents">
          <Button size="lg" className="cursor-pointer mt-8 pointer-events-auto" style={{ backgroundColor: "rgba(0,0,0,0.75)" }}>See agent showcase</Button>
        </Link>
      </div>
      <div className="splashoverlay" />
      <Canvas flat shadows camera={{ position: [-15, 0, 10], fov: 25 }} style={{ position: 'fixed' }}>
        <fog attach="fog" args={['black', 15, 21.5]} />
        <Stage intensity={0.5} environment="city" shadows={{ type: 'accumulative', bias: -0.001, intensity: Math.PI }} adjustCamera={false}>
          <Kamdo rotation={[0, Math.PI, 0]} />
        </Stage>
        <Grid renderOrder={-1} position={[0, -1.85, 0]} infiniteGrid cellSize={0.6} cellThickness={0.6} sectionSize={3.3} sectionThickness={1.5} sectionColor={[0.5, 0.5, 10]} fadeDistance={30} />
        <OrbitControls autoRotate autoRotateSpeed={-0.35} enableZoom={false} makeDefault minPolarAngle={Math.PI / 2} maxPolarAngle={Math.PI / 2} />
        <EffectComposer disableNormalPass>
          <Bloom luminanceThreshold={2} mipmapBlur />
          <ToneMapping />
        </EffectComposer>
        <Environment background preset="sunset" blur={0.8} />
      </Canvas>
    </>
  )
}



/*
Auto-generated by: https://github.com/pmndrs/gltfjsx
Command: npx gltfjsx@6.1.0 s2wt_kamdo_industrial_divinities.glb --transform --simplify
Author: Hansalex (https://sketchfab.com/Hansalex)
License: CC-BY-4.0 (http://creativecommons.org/licenses/by/4.0/)
Source: https://sketchfab.com/3d-models/s2wt-kamdo-industrial-divinities-f503b70ac05e49a38c81100d71599a1b
Title: S2WT "Kamdo" (Industrial Divinities)
*/

function Kamdo(props) {
  const head = useRef()
  const stripe = useRef()
  const light = useRef()
  const { nodes, materials } = useGLTF('/models/s2wt_kamdo_industrial_divinities-transformed.glb')
  useFrame((state, delta) => {
    const t = (1 + Math.sin(state.clock.elapsedTime * 2)) / 2
    stripe.current.color.setRGB(2 + t * 10, 2, 20 + t * 50)
    easing.dampE(head.current.rotation, [0, state.pointer.x * (state.camera.position.z > 1 ? 1 : -1), 0], 0.4, delta)
    light.current.intensity = 1 + t * 2
  })
  return (
    <group {...props}>
      <mesh castShadow receiveShadow geometry={nodes.body001.geometry} material={materials.Body} />
      <group ref={head}>
        <mesh castShadow receiveShadow geometry={nodes.head001.geometry} material={materials.Head} />
        <mesh castShadow receiveShadow geometry={nodes.stripe001.geometry}>
          <meshBasicMaterial ref={stripe} toneMapped={false} />
          <pointLight ref={light} intensity={1} color={[10, 2, 5]} distance={2.5} />
        </mesh>
      </group>
    </group>
  )
}

useGLTF.preload('/models/s2wt_kamdo_industrial_divinities-transformed.glb')

Splash.layout = (page) => <NavLayout children={page} />

export default Splash
