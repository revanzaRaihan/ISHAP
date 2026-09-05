// import { FEATURES_LIST } from '@/lib/constants';

// export default function FeatureCards() {
//   return (
//     <section id="fitur" className="py-20 max-w-6xl mx-auto px-6">
//       <div className="text-center max-w-2xl mx-auto mb-16 space-y-3">
//         <h2 className="text-3xl font-bold text-slate-800">
//           Solusi Kesehatan Pernapasan Terpadu
//         </h2>
//         <p className="text-slate-500 text-sm leading-relaxed">
//           Platform sederhana untuk membantu deteksi dini gejala ISPA dan pemantauan lingkungan harian Anda.
//         </p>
//       </div>

//       <div className="grid md:grid-cols-3 gap-8">
//         {FEATURES_LIST.map((feature, idx) => (
//           <div
//             key={idx}
//             className="bg-white p-8 rounded-3xl border border-slate-100 shadow-[0_2px_15px_rgba(0,0,0,0,0.02)] hover:shadow-[0_8px_25px_rgba(0,0,0,0,0.04)] transition duration-300 space-y-4"
//           >
//             <div className="w-12 h-12 bg-secondaryBlue text-2xl flex items-center justify-center rounded-2xl">
//               {feature.icon}
//             </div>
//             <h3 className="text-lg font-bold text-slate-800">{feature.title}</h3>
//             <p className="text-slate-500 text-sm leading-relaxed">{feature.description}</p>
//           </div>
//         ))}
//       </div>
//     </section>
//   );
// }