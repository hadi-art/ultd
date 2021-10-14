<?php

namespace App\Http\Controllers;

use Log;
use App\Traits\ApiVersion;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    use ApiVersion;

    /**
     * Get version number and details
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $result = $this->getVersion($request->segment(3));

        if (!$result) {
            return response()->json([
                'success' => false,
                'code' => 'not_found',
                'message' => 'Incorrect version provided',
                'data' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'code' => 'success',
            'message' => 'success',
            'data' => $result
        ]);
    }
    public function all()
    {
        $result = $this->list_version();

        return response()->json([
            'success' => true,
            'code' => 'success',
            'message' => 'success',
            'data' => $result
        ]);
    }
}
