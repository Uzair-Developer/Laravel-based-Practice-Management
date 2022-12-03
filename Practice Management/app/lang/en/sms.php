<?php

return array(

    'create' => ':patient_name
Saudi-German Hosp. confirms your reservation no. :reservationCode
at :time_from on :date
Dr.:physician_name-:clinic_name
To modify Please call 2685511',
    ////////////////////////////////
    'revisit' => ':patient_name
Saudi-German Hosp. confirms your revisit no. :reservationCode
at :time_from on :date
Dr.:physician_name-:clinic_name
To modify Please call 2685511',
    ////////////////////////////////
    'modify' => ':patient_name
Saudi-German Hosp. MODIFIED your reservation no. :reservationCode
at :time_from on :date
Dr.:physician_name-:clinic_name
To modify Please call 2685511',
    ////////////////////////////////
    'cancel' => ':patient_name
Saudi-German Hosp. confirms that your reservation no. :reservationCode
at :time_from on :date
Dr.:physician_name-:clinic_name is Cancelled',
    ////////////////////////////////
    'pending' => ':patient_name
We are sorry to inform you that reservation no. :reservationCode
Dr.:physician_name(is not available)
To modify Please call 2685511',
    ////////////////////////////////
    'reminder' => ':patient_name
Saudi-German Hosp. reminds you about your reservation tomorrow at :time_from in Dr.:physician_name clinic
Please come 15 minuets earlier to prepare your file
For inquiry please call 2685511',
////////////////////////////////
////////////////////////////////
////////////////////////////////
////////////////////////////////
////////////////////////////////
    'create-ar' => ':patient_name
مستشفى السعودى الألمانى - الرياض
تؤكد حجزكم رقم :reservationCode
بتاريخ :date
الساعه :time_from
:clinic_name
د.:physician_name
ولتعديل الحجز برجاء الإتصال 2685511',
    ////////////////////////////////
    'revisit-ar' => ':patient_name
مستشفى السعودى الألمانى - الرياض
تؤكد حجز الاستشاره رقم :reservationCode
بتاريخ :date
الساعه :time_from
:clinic_name
د.:physician_name
ولتعديل الحجز برجاء الإتصال 2685511',
    ////////////////////////////////
    'modify-ar' => ':patient_name
مستشفى السعودى الألمانى - الرياض
تم تعديل الحجز رقم :reservationCode
بتاريخ :date
الساعه :time_from
:clinic_name
د.:physician_name
وللتعديل برجاء الإتصال 2685511',
    'cancel-ar' => ':patient_name
تم إلغاء الحجز :reservationCode د.:physician_name
للإتصال 2685511',
    ///////////////////////////////
    'pending-ar' => 'مستشفى السعودى الألمانى - الرياض
نعتذر عن تغير موعد حجزكم رقم :reservationCode
:clinic_name
بإسم :patient_name
وذلك لظرف طارئ ولتعديل الحجز برجاء الإتصال على رقم 2685511',
    ////////////////////////////////
    'reminder-ar' => ':patient_name
مستشفى السعودى الألمانى - الرياض تذكركم بموعدكم غدا الساعة :time_from فى عياده د.:physician_name
برجاء الحضور قبل الموعد ب15 دقيقة لتجهيز الملف الطبي
وللاستفسار فضلا اتصل على 2685511'
);
