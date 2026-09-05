export interface Feature {
  title: string;
  description: string;
  icon: string;
}

export const FEATURES_LIST: Feature[] = [
  {
    title: 'Skrining Dini Berbasis AI',
    description: 'Analisis gejala awal ISPA dengan cepat dan akurat dalam beberapa langkah sederhana.',
    icon: '🩺',
  },
  {
    title: 'Pemantauan Kualitas Udara',
    description: 'Informasi indeks AQI wilayah Balikpapan secara real-time untuk kewaspadaan harian.',
    icon: '🌬️',
  },
  {
    title: 'Rujukan Faskes Terdekat',
    description: 'Rekomendasi lokasi Puskesmas dan Rumah Sakit terdekat jika terdeteksi risiko tinggi.',
    icon: '🏥',
  },
];