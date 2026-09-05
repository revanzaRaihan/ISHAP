import { ScreeningChat } from '@/features/screening/components/ScreeningChat';

export const metadata = {
	title: 'Skrining Mandiri ISPA | I-SHAP',
	description: 'Pilih gejala untuk melihat perkiraan risiko ISPA.',
};

export default function ScreeningPage() {
	return <ScreeningChat />;
}
