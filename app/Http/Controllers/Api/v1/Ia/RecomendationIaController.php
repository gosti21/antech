<?php

namespace App\Http\Controllers\Api\v1\Ia;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\Ia\ProductIaResource;
use App\Services\Api\v1\Ia\RecommendationIAService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecomendationIaController extends Controller
{
    public function __construct(
        private RecommendationIAService $service
    ) {}

    /**
     * POST /api/v1/ia/recommend
     *
     * Obtiene recomendaciones de productos basadas en IA.
     *
     * Body:
     * {
     *   "query": "necesito un mouse gaming",
     *   "conversation_id": "uuid-opcional"
     * }
     */
    public function recommend(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:3|max:500',
            'conversation_id' => 'nullable|string'
        ]);

        try {
            $result = $this->service->recommend(
                query: $request->input('query'),
                conversationId: $request->input('conversation_id')
            );

            return response()->json([
                'status' => true,
                'message' => 'Comunicación exitosa',
                'data' => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al procesar la recomendación',
                'message' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * POST /api/v1/ia/sync-catalog
     *
     * Sincroniza manualmente el catálogo con la IA.
     *
     * Útil para:
     * - Primera sincronización
     * - Resetear el catálogo
     * - Forzar actualización completa
     */
    public function syncCatalog(): JsonResponse
    {
        try {
            $result = $this->service->syncCatalog();

            return response()->json([
                'status' => true,
                'message' => 'Catálogo sincronizado exitosamente',
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al sincronizar catálogo',
                'message' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor'
            ], 500);
        }
    }
}
