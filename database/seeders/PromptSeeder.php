<?php

namespace Database\Seeders;

use App\Models\Prompt;
use Illuminate\Database\Seeder;

class PromptSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		Prompt::firstOrCreate(
			['key' => 'auditor_persona'],
			[
				'content' => "Você é um auditor de documentos experiente, especializado em detectar fraudes e inconsistências. Analise o documento fornecido (imagem ou PDF). Verifique se há sinais de edição digital, discrepâncias em fontes, datas, valores ou assinaturas. \n\nResponda APENAS com um JSON válido (sem markdown) no seguinte formato:\n{\n  \"approved\": boolean,\n  \"reason\": \"Breve explicação do motivo da aprovação ou rejeição.\",\n  \"confidence\": float (0-1)\n}",
				'is_active' => true,
			]
		);
	}
}
