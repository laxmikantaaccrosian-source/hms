<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateWebAppointmentRequest;
use App\Models\Patient;
use App\Models\User;
use App\Repositories\AppointmentRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends AppBaseController
{
    /** @var AppointmentRepository */
    private $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepo)
    {
        $this->appointmentRepository = $appointmentRepo;
    }

    public function store(CreateWebAppointmentRequest $request)
    {
        $input = $request->all();
        $input['opd_date'] = $input['opd_date'].$input['time'];
        $input['status'] = true;
        try {
            DB::beginTransaction();
            if ($input['patient_type'] == 2 && ! empty($input['patient_type'])) {
                foreach ($input as $key => $value) {
                    if (strpos($key, 'field') === 0) {
                        $jsonFields[$key] = $value;
                    }
                }

                $input['custom_field'] = $jsonFields;
                $this->appointmentRepository->create($input);
            }

            if ($input['patient_type'] == 1 && ! empty($input['patient_type'])) {
                $emailExists = User::whereEmail($input['email'])->exists();
                if ($emailExists) {
                    return $this->sendError(__('messages.appointment.old_patient_email_exists'));
                }
                $this->appointmentRepository->createNewAppointment($input);
            }

            DB::commit();

            return $this->sendSuccess(__('messages.web_menu.appointment').' '.__('messages.common.saved_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError(__('messages.appointment.appointment_exists'));
        } catch (\Throwable $e) {
        }
    }

    public function getDoctors(Request $request)
    {
        $id = $request->get('id');

        $doctors = $this->appointmentRepository->getDoctors($id);

        return $this->sendResponse($doctors, 'Retrieved successfully');
    }

    public function getDoctorList(Request $request)
    {
        $id = $request->get('id');
        $doctorArr = $this->appointmentRepository->getDoctorList($id);

        return $this->sendResponse($doctorArr, 'Retrieved successfully');
    }

    public function getBookingSlot(Request $request)
    {
        $inputs = $request->all();
        $inputs['editSelectedDate'] = Carbon::parse($inputs['editSelectedDate'])->format('Y-m-d');
        $data = $this->appointmentRepository->getBookingSlot($inputs);

        return $this->sendResponse($data, 'Retrieved successfully');
    }

    public function getPatientDetails($email)
    {
        $patient = Patient::with('patientUser')->get()->where('patientUser.status', '=', 1)->where('patientUser.email', $email)->first();
        $data = null;
        if ($patient != null) {
            $data = [
                $patient->id => $patient->patientUser->full_name,
            ];
        }

        return $this->sendResponse($data, 'User Retrieved Successfully');
    }
}
