<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ExceptionTrait
{
    public function apiException($request, $ex){
        if($this->isModel($ex)){
            return $this->modelResponse($ex);
        }

        if($this->isHttp($ex)){
            return $this->httpResponse($ex);
        }
    }

    private function isModel($ex){
        return $ex instanceof ModelNotFoundException;
    }

    private function modelResponse($ex){
        return response()->json([
            'error'=>'Data Not Found',
        ], Response::HTTP_NOT_FOUND);
    }

    private function isHttp($ex){

        return $ex instanceof NotFoundHttpException;
    }

    private function httpResponse($ex){
        return response()->json([
            'error'=>'Incorrect Route',
        ], Response::HTTP_BAD_REQUEST);
    }
}